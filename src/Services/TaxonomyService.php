<?php

namespace RezaQsr\Wp2Laravel\Services;


use RezaQsr\Wp2Laravel\Repositories\DBTaxonomyRepository;

class TaxonomyService
{
    protected $repo;

    public function __construct(DBTaxonomyRepository $repo)
    {
        $this->repo = $repo;
    }
    public function getTaxonomy(string $taxonomy)
    {
        return $this->repo->getTaxonomy($taxonomy);
    }
    public function getTaxonomies(array $args = [])
    {
        return $this->repo->getTaxonomies($args);
    }

}
