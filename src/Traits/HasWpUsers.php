<?php

namespace RezaQsr\Wp2Laravel\Traits;

use RezaQsr\Wp2Laravel\Services\UserService;

trait HasWpUsers
{
    protected function usersService(): UserService
    {
        return app(UserService::class);
    }
    public function insert_user(array $data)
    {
        return $this->usersService()->insertUser($data);
    }
    public function update_user(int $id, array $data): bool
    {
        return $this->usersService()->updateUser($id, $data);
    }
    public function delete_user(int $id): bool
    {
        return $this->usersService()->deleteUser($id);
    }

    public function get_user_meta(int $userId, string $key, $default = null)
    {
        return $this->usersService()->getUserMeta($userId, $key, $default);
    }
    public function has_user_meta(int $userId, string $key)
    {
        return $this->usersService()->hasUserMeta($userId, $key);
    }
    public function update_user_meta(int $userId, string $key, $value): bool
    {
        return $this->usersService()->updateUserMeta($userId, $key, $value);
    }
    public function delete_user_meta(int $userId, string $key): bool
    {
        return $this->usersService()->deleteUserMeta($userId, $key);
    }

    public function get_user_roles(int $userId): array
    {
        return $this->usersService()->getUserRoles($userId);
    }
}
