<?php

namespace RezaQsr\Wp2Laravel\Repositories;

use RezaQsr\Wp2Laravel\Contracts\TermRepositoryInterface;
use RezaQsr\Wp2Laravel\Models\Term;
use RezaQsr\Wp2Laravel\Models\TermTaxonomy;
use RezaQsr\Wp2Laravel\Models\TermRelationship;
use Illuminate\Support\Str;
class DBTermRepository implements TermRepositoryInterface
{
    public function getTerms(array $args = [])
    {
        $query = Term::query()->with('taxonomies');

        if (!empty($args['taxonomy'])) {
            $query->whereHas('taxonomies', function ($q) use ($args) {
                $q->whereIn('taxonomy', (array) $args['taxonomy']);
            });
        }

        if (!empty($args['slug'])) {
            $query->whereIn('slug', (array) $args['slug']);
        }

        if (!empty($args['search'])) {
            $query->where('name', 'like', '%' . $args['search'] . '%');
        }

        return $query->get();
    }
    public function getTermBy(string $field, $value, string $taxonomy)
    {
        $query = Term::query()->with('taxonomies');

        if (!empty($taxonomy)) {
            $query->whereHas('taxonomies', function ($q) use ($taxonomy) {
                $q->where('taxonomy', $taxonomy);
            });
        }
        if (!empty($field) && !empty($value)) {
            $query->where($field, $value);
        }
        return $query->get();
    }
    public function insertTerm(string $term, string $taxonomy, array $args = [])
    {
        $slug = $args['slug'] ?? Str::slug($term);
        $description = $args['description'] ?? null;

        $termModel = Term::firstOrCreate(
            ['slug' => $slug],
            ['name' => $term]
        );

        $taxonomyModel = TermTaxonomy::firstOrCreate(
            ['term_id' => $termModel->term_id, 'taxonomy' => $taxonomy],
            ['description' => $description, 'parent' => $args['parent'] ?? 0]
        );

        return [$termModel, $taxonomyModel];
    }

    public function deleteTerm(int $termId, string $taxonomy): bool
    {
        $termTax = TermTaxonomy::where('term_id', $termId)
            ->where('taxonomy', $taxonomy)
            ->first();

        if (!$termTax) return false;

        TermRelationship::where('term_taxonomy_id', $termTax->term_taxonomy_id)->delete();
        $termTax->delete();

        if (!TermTaxonomy::where('term_id', $termId)->exists()) {
            Term::find($termId)?->delete();
        }

        return true;
    }
    public function setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false)
    {
        if (!$append) {
            TermRelationship::where('object_id', $postId)
                ->whereHas('taxonomy', function ($q) use ($taxonomy) {
                    $q->where('taxonomy', $taxonomy);
                })->delete();
        }

        foreach ($terms as $termId) {
            TermRelationship::firstOrCreate([
                'object_id' => $postId,
                'term_taxonomy_id' => $termId,
            ]);
        }
        return true;
    }
}
