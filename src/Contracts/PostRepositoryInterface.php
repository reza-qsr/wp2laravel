<?php

namespace RezaQsr\Wp2Laravel\Contracts;

interface PostRepositoryInterface
{
    public function find(int $id);
    public function query(array $args = []);
    public function insert(array $data);
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;

    public function getMeta(int $postId, string $key, $default = null);
    public function updateMeta(int $postId, string $key, $value): bool;
    public function deleteMeta(int $postId, string $key): bool;
}
