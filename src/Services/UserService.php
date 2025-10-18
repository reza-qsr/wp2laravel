<?php

namespace RezaQsr\Wp2Laravel\Services;

use RezaQsr\Wp2Laravel\Repositories\DbUserRepository;

class UserService
{
    protected $repo;

    public function __construct(DbUserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function insertUser(array $data)
    {
        return $this->repo->insert($data);
    }

    public function updateUser(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteUser(int $id)
    {
        return $this->repo->delete($id);
    }

    public function getUserMeta(int $userId, string $key, $default = null)
    {
        return $this->repo->getMeta($userId, $key, $default);
    }

    public function hasUserMeta(int $userId, string $key)
    {
        return $this->repo->hasMeta($userId, $key);
    }

    public function updateUserMeta(int $userId, string $key, $value): bool
    {
        return $this->repo->updateMeta($userId, $key, $value);
    }

    public function deleteUserMeta(int $userId, string $key): bool
    {
        return $this->repo->deleteMeta($userId, $key);
    }

    public function getUserRoles(int $userId): array
    {
        return $this->repo->getRoles($userId);
    }
}
