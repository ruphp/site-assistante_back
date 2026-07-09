<?php

namespace app\Application\Panel;

use app\Application\Panel\Dto\ManagerOperatorView;
use app\Infrastructure\YiiActiveRecord\Users;
use app\Modules\Support\Application\Contract\SupportPushDeviceRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportPushNotificationSenderInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportPlanLimit;
use app\Presentation\Http\Form\ManagerOperatorForm;
use Yii;

final class ManagerOperatorService
{
    public function __construct(
        private readonly SupportSettingsRepositoryInterface $supportSettings,
        private readonly SupportPushDeviceRepositoryInterface $pushDevices,
        private readonly SupportPushNotificationSenderInterface $pushSender,
    ) {
    }

    /**
     * @return ManagerOperatorView[]
     */
    public function listForOwner(int $ownerPublicKey): array
    {
        $rows = Users::find()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.telegram',
                'users.max_contact',
                'users.public_key',
            ])
            ->innerJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where([
                'users.public_key' => $ownerPublicKey,
                'users.status' => Users::STATUS_ACTIVE,
                'auth_assignment.item_name' => 'manager',
            ])
            ->orderBy(['users.id' => SORT_ASC])
            ->asArray()
            ->all();

        return array_map(
            static fn(array $row): ManagerOperatorView => new ManagerOperatorView(
                id: (int)$row['id'],
                name: (string)$row['name'],
                email: (string)$row['email'],
                phone: (string)($row['phone'] ?? ''),
                telegram: (string)($row['telegram'] ?? ''),
                maxContact: (string)($row['max_contact'] ?? ''),
                isOwner: (int)$row['id'] === $ownerPublicKey,
            ),
            $rows,
        );
    }

    public function canManage(int $ownerPublicKey, int $currentUserId): bool
    {
        return $ownerPublicKey === $currentUserId && $this->operatorLimit($ownerPublicKey) > 1;
    }

    public function operatorLimit(int $ownerPublicKey): int
    {
        $settings = $this->supportSettings->getForClient($ownerPublicKey);

        return SupportPlanLimit::forPlan($settings->plan)->maxOperators;
    }

    public function create(int $ownerPublicKey, ManagerOperatorForm $form): ?string
    {
        $used = count($this->listForOwner($ownerPublicKey));
        if ($used >= $this->operatorLimit($ownerPublicKey)) {
            $form->addError('email', 'Лимит менеджеров по тарифу исчерпан');
            return null;
        }

        $owner = Users::findOne(['id' => $ownerPublicKey, 'public_key' => $ownerPublicKey]);
        if (!$owner instanceof Users) {
            $form->addError('email', 'Владелец не найден');
            return null;
        }

        $password = Users::gen_password(10);
        $operator = new Users();
        $operator->name = trim($form->name);
        $operator->email = mb_strtolower(trim($form->email));
        $operator->firm = $owner->firm;
        $operator->public_key = $ownerPublicKey;
        $operator->phone = trim($form->phone);
        $operator->telegram = trim($form->telegram);
        $operator->max_contact = trim($form->maxContact);
        $operator->status = Users::STATUS_ACTIVE;
        $operator->setPassword($password);

        if (!$operator->save()) {
            foreach ($operator->getFirstErrors() as $attribute => $error) {
                $form->addError($attribute === 'email' ? 'email' : 'name', $error);
            }
            return null;
        }

        $this->assignManagerRole($operator);
        $this->notifyOwnerAboutPassword($ownerPublicKey, $operator, $password, 'Создан менеджер');

        return $password;
    }

    public function resetPassword(int $ownerPublicKey, int $operatorId): ?string
    {
        $operator = $this->operator($ownerPublicKey, $operatorId);
        if (!$operator instanceof Users || (int)$operator->id === $ownerPublicKey) {
            return null;
        }

        $password = Users::gen_password(10);
        $operator->setPassword($password);
        if (!$operator->save()) {
            return null;
        }

        $this->notifyOwnerAboutPassword($ownerPublicKey, $operator, $password, 'Сброс пароля менеджера');

        return $password;
    }

    public function disable(int $ownerPublicKey, int $operatorId): bool
    {
        $operator = $this->operator($ownerPublicKey, $operatorId);
        if (!$operator instanceof Users || (int)$operator->id === $ownerPublicKey) {
            return false;
        }

        $operator->status = Users::STATUS_PENDING;

        return $operator->save(false, ['status']);
    }

    private function operator(int $ownerPublicKey, int $operatorId): ?Users
    {
        return Users::find()
            ->innerJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where([
                'users.id' => $operatorId,
                'users.public_key' => $ownerPublicKey,
                'users.status' => Users::STATUS_ACTIVE,
                'auth_assignment.item_name' => 'manager',
            ])
            ->one();
    }

    private function assignManagerRole(Users $operator): void
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('manager');

        if ($role !== null && $auth->getAssignment('manager', (int)$operator->id) === null) {
            $auth->assign($role, (int)$operator->id);
        }
    }

    private function notifyOwnerAboutPassword(int $ownerPublicKey, Users $operator, string $password, string $title): void
    {
        $tokens = $this->pushDevices->activeTokensForUser($ownerPublicKey);
        if ($tokens === []) {
            return;
        }

        $body = sprintf('%s: %s. Пароль: %s', $operator->name, $operator->email, $password);
        foreach ($tokens as $token) {
            $this->pushSender->sendToToken($token, $title, $body, [
                'type' => 'manager_password',
                'manager_id' => (string)$operator->id,
                'manager_email' => (string)$operator->email,
            ]);
        }
    }
}
