<?php

namespace app\Infrastructure\Admin;

use app\Application\Admin\Contract\ClientRepositoryInterface;
use app\Application\Admin\Dto\CreateClientRequest;
use app\Application\Admin\Dto\UpdateClientRequest;
use app\Infrastructure\YiiActiveRecord\Users;

final class YiiClientRepository implements ClientRepositoryInterface
{
    public function listManagers(): array
    {
        return Users::getListUsersManager();
    }

    public function create(CreateClientRequest $request): int
    {
        $user = new Users();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->firm = $request->firm;
        $user->public_key = null;
        $user->setPassword($request->password);
        $user->status = 1;
        $user->save();

        return (int)$user->id;
    }

    public function deleteInactive(int $userId): bool
    {
        $user = $this->findManager($userId);

        if ($user === null || $user->status) {
            return false;
        }

        return (bool)$user->delete();
    }

    public function update(UpdateClientRequest $request, ?string $plainPassword): bool
    {
        $user = $this->findManager($request->id);

        if ($user === null) {
            return false;
        }

        $user->firm = $request->firm;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->gmt = $request->gmt;

        if ($plainPassword !== null) {
            $user->setPassword($plainPassword);
        }

        return $user->save();
    }

    public function findForAdminView(int $userId): mixed
    {
        return $this->findManager($userId);
    }

    private function findManager(int $userId): ?Users
    {
        return Users::find()
            ->innerJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['users.id' => $userId, 'auth_assignment.item_name' => 'manager'])
            ->one();
    }
}
