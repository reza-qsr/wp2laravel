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
    public function hasPostMeta(int $postId, string $key);
    public function updatePostMeta(int $postId, string $key, $value);
    public function deletePostMeta(int $postId, string $key);

    public function getTerms(array $args = []);
    public function getTermBy(string $field, $value, string $taxonomy);
    public function insertTerm(string $term, string $taxonomy, array $args = []);
    public function updateTerm(int $termId, string $taxonomy, array $args = []);
    public function deleteTerm(int $termId, string $taxonomy);
    public function setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false);
    public function getPostTerms(int $postId, string $taxonomy);

    public function getTaxonomy(string $taxonomy);
    public function getTaxonomies(array $args = []);


    public function insertUser(array $data);
    public function updateUser(int $id, array $data): bool;
    public function deleteUser(int $id): bool;
    public function getUserMeta(int $userId, string $key, $default = null);
    public function hasUserMeta(int $userId, string $key): bool;
    public function updateUserMeta(int $userId, string $key, $value): bool;
    public function deleteUserMeta(int $userId, string $key): bool;
    public function getUserRoles(int $userId): array;
}
