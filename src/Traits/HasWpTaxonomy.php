<?php

namespace RezaQsr\Wp2Laravel\Traits;

use RezaQsr\Wp2Laravel\Services\TaxonomyService;

trait HasWpTaxonomy
{
    protected function taxonomyService(): TaxonomyService
    {
        return app(TaxonomyService::class);
    }

    public function getTaxonomy(string $taxonomy)
    {
        return $this->taxonomyService()->getTaxonomy($taxonomy);
    }
    public function getTaxonomies(array $args = [])
    {
        return $this->taxonomyService()->getTaxonomies($args);
    }

}
