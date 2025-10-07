<?php

namespace RezaQsr\Wp2Laravel\Contracts;

 interface TermRepositoryInterface
 {
     public function getTerms(array $args = []);
     public function getTermBy(string $field, $value, string $taxonomy);
     public function insertTerm(string $term, string $taxonomy, array $args = []);
     public function updateTerm(int $termId, string $taxonomy, array $args = []);
     public function deleteTerm(int $termId, string $taxonomy): bool;
     public function setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false);
     public function getPostTerms(int $postId, string $taxonomy);
 }