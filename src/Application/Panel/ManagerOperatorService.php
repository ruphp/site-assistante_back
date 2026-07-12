<?php

namespace app\Application\Panel;

use app\Application\Panel\Dto\ManagerOperatorView;
use app\Infrastructure\YiiActiveRecord\Users;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportPlanLimit;
use app\Presentation\Http\Form\ManagerOperatorForm;
use app\Presentation\Http\Form\ManagerOwnerContactForm;
use Yii;
use yii\helpers\FileHelper;

final class ManagerOperatorService
{
    public function __construct(
        private readonly SupportSettingsRepositoryInterface $supportSettings,
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
                'users.avatar_path',
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
            fn(array $row): ManagerOperatorView => new ManagerOperatorView(
                id: (int)$row['id'],
                name: (string)$row['name'],
                email: (string)$row['email'],
                phone: (string)($row['phone'] ?? ''),
                telegram: (string)($row['telegram'] ?? ''),
                maxContact: (string)($row['max_contact'] ?? ''),
                avatarUrl: $this->avatarUrl($row['avatar_path'] ?? null),
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

    public function ownerContactForm(int $ownerPublicKey): ManagerOwnerContactForm
    {
        return $this->profileForm($ownerPublicKey);
    }

    public function profileForm(int $userId): ManagerOwnerContactForm
    {
        $owner = Users::findOne($userId);
        $form = new ManagerOwnerContactForm();

        if ($owner instanceof Users) {
            $form->name = (string)$owner->name;
            $form->phone = (string)($owner->phone ?? '');
            $form->telegram = (string)($owner->telegram ?? '');
            $form->maxContact = (string)($owner->max_contact ?? '');
        }

        return $form;
    }

    public function profileAvatarUrl(int $userId): ?string
    {
        $user = Users::findOne($userId);

        return $user instanceof Users ? $this->avatarUrl($user->avatar_path ?? null) : null;
    }

    public function updateOwnerContacts(int $ownerPublicKey, ManagerOwnerContactForm $form): bool
    {
        return $this->updateProfile($ownerPublicKey, $form);
    }

    public function updateProfile(int $userId, ManagerOwnerContactForm $form): bool
    {
        $owner = Users::findOne($userId);
        if (!$owner instanceof Users) {
            $form->addError('name', 'Владелец не найден');
            return false;
        }

        $owner->name = trim($form->name);
        $owner->phone = trim($form->phone);
        $owner->telegram = trim($form->telegram);
        $owner->max_contact = trim($form->maxContact);

        if ($form->avatar !== null) {
            $owner->avatar_path = $this->saveAvatar((int)$owner->id, $form->avatar->extension, $form->avatar->tempName);
        }

        if ($owner->save(false, ['name', 'phone', 'telegram', 'max_contact', 'avatar_path'])) {
            return true;
        }

        foreach ($owner->getFirstErrors() as $error) {
            $form->addError('name', $error);
        }

        return false;
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

        if ($form->avatar !== null) {
            $operator->avatar_path = $this->saveAvatar((int)$operator->id, $form->avatar->extension, $form->avatar->tempName);
            $operator->save(false, ['avatar_path']);
        }

        $this->assignManagerRole($operator);
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

    private function saveAvatar(int $userId, string $extension, string $temporaryPath): string
    {
        $directory = Yii::getAlias('@webroot/uploads/operators');
        try {
            FileHelper::createDirectory($directory, 0775);
        } catch (\Throwable $exception) {
            Yii::error([
                'message' => 'Failed to create operator avatar directory',
                'directory' => $directory,
                'error' => $exception->getMessage(),
            ], __METHOD__);

            throw new \RuntimeException('Не удалось создать каталог аватаров');
        }

        $extension = strtolower($extension === 'jpeg' ? 'jpg' : $extension);
        $relativePath = '/uploads/operators/' . $userId . '.' . $extension;
        $absolutePath = Yii::getAlias('@webroot') . $relativePath;
        if (!move_uploaded_file($temporaryPath, $absolutePath) && !rename($temporaryPath, $absolutePath)) {
            throw new \RuntimeException('Не удалось сохранить аватар');
        }

        return $relativePath;
    }

    private function avatarUrl(?string $avatarPath): ?string
    {
        $avatarPath = trim((string)$avatarPath);

        return $avatarPath === '' ? null : $avatarPath;
    }

}
