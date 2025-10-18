<?php

namespace RezaQsr\Wp2Laravel\Contracts;

interface UserRepositoryInterface
{
    public function find(int $id);
    public function insert(array $data);
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getMeta(int $userId, string $key, $default = null);
    public function hasMeta(int $userId, string $key): bool;
    public function updateMeta(int $userId, string $key, $value): bool;
    public function deleteMeta(int $userId, string $key): bool;
    public function getRoles(int $userId): array;
}
