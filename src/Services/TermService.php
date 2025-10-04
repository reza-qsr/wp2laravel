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

    public function get_terms(array $args = [])
    {
        return $this->repo->getTerms($args);
    }

    public function insert_term(string $term, string $taxonomy, array $args = [])
    {
        return $this->repo->insertTerm($term, $taxonomy, $args);
    }

    public function set_post_terms(int $postId, array $terms, string $taxonomy, bool $append = false)
    {
        return $this->repo->setPostTerms($postId, $terms, $taxonomy, $append);
    }
}
