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

    public function insertTerm(string $term, string $taxonomy, array $args = [])
    {
        return $this->repo->insertTerm($term, $taxonomy, $args);
    }

    public function setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false)
    {
        return $this->repo->setPostTerms($postId, $terms, $taxonomy, $append);
    }
}
