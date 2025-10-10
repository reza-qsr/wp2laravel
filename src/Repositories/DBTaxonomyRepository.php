<?php

namespace RezaQsr\Wp2Laravel\Repositories;

use RezaQsr\Wp2Laravel\Contracts\TaxonomyRepositoryInterface;
use RezaQsr\Wp2Laravel\Models\TermTaxonomy;

class DBTaxonomyRepository implements TaxonomyRepositoryInterface
{
    public function getTaxonomy(string $taxonomy)
    {
        $taxonomyData = TermTaxonomy::where('taxonomy', $taxonomy)->first();
        if (!$taxonomyData) {
            return null;
        }

        return (object) [
            'name'        => $taxonomyData->taxonomy,
            'description' => $taxonomyData->description,
            'parent'      => $taxonomyData->parent,
            'count'       => $taxonomyData->count,
            'hierarchical' => $taxonomyData->parent > 0,
        ];
    }
    public function getTaxonomies(array $args = [])
    {
        $query = TermTaxonomy::query()
            ->select('taxonomy')
            ->distinct();

        if (!empty($args['taxonomy'])) {
            $query->whereIn('taxonomy', (array) $args['taxonomy']);
        }

        $taxonomies = $query->pluck('taxonomy')->toArray();

        $results = [];
        foreach ($taxonomies as $taxonomy) {
            $results[$taxonomy] = $this->getTaxonomy($taxonomy);
        }

        return $results;
    }
}
