<?php

namespace RezaQsr\Wp2Laravel\Contracts;

interface Wp2LaravelManagerInterface
{
    public function getOption(string $key, $default = null);
    public function updateOption(string $key, $value): bool;
    public function deleteOption(string $key): bool;
    public function addOption(string $key, $value, string $autoload = 'yes'): bool;

    public function getPost(int $id);
    public function getPosts(array $args = []);
    public function insertPost(array $data);
    public function updatePost(int $id, array $data);
    public function deletePost(int $id);

    public function getPostMeta(int $postId, string $key, $default = null);
    public function updatePostMeta(int $postId, string $key, $value);
    public function deletePostMeta(int $postId, string $key);
}
