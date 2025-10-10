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
}
