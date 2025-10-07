<?php

namespace RezaQsr\Wp2Laravel\Services;

use RezaQsr\Wp2Laravel\Repositories\DBTermRepository;

class TermService
{
    protected $repo;

    public function __construct(DBTermRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getTerms(array $args = [])
    {
        return $this->repo->getTerms($args);
    }
    public function getTermBy(string $field, $value, string $taxonomy)
    {
        return $this->repo->getTermBy($field, $value, $taxonomy);
    }
    public function insertTerm(string $term, string $taxonomy, array $args = [])
    {
        return $this->repo->insertTerm($term, $taxonomy, $args);
    }
    public function updateTerm(int $termId, string $taxonomy, array $args = [])
    {
        return $this->repo->updateTerm($termId, $taxonomy, $args);
    }
    public function deleteTerm(int $termId, string $taxonomy)
    {
        return $this->repo->deleteTerm($termId, $taxonomy);
    }
    public function setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false)
    {
        return $this->repo->setPostTerms($postId, $terms, $taxonomy, $append);
    }
    public function getPostTerms(int $postId, string $taxonomy)
    {
        return $this->repo->getPostTerms($postId, $taxonomy);
    }

}
