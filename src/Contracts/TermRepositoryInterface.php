<?php

namespace RezaQsr\Wp2Laravel\Contracts;

 interface TermRepositoryInterface
 {
     public function get(array $args = []);
     public function getBy(string $field, $value, string $taxonomy);
     public function insert(string $term, string $taxonomy, array $args = []);
     public function update(int $termId, string $taxonomy, array $args = []);
     public function delete(int $termId, string $taxonomy): bool;
     public function setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false);
     public function getPostTerms(int $postId, string $taxonomy);
 }