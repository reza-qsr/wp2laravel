<?php

namespace RezaQsr\Wp2Laravel\Contracts;

 interface TermRepositoryInterface
 {
     public function getTerms(array $args = []);
     public function insertTerm(string $term, string $taxonomy, array $args = []);
     public function setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false);
 }