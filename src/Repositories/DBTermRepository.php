<?php

namespace RezaQsr\Wp2Laravel\Repositories;

use RezaQsr\Wp2Laravel\Contracts\TermRepositoryInterface;
use RezaQsr\Wp2Laravel\Models\Term;
use RezaQsr\Wp2Laravel\Models\TermTaxonomy;
use RezaQsr\Wp2Laravel\Models\TermRelationship;
use Illuminate\Support\Str;
class DBTermRepository implements TermRepositoryInterface
{
    public function get(array $args = [])
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
    public function getBy(string $field, $value, string $taxonomy)
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
    public function insert(string $term, string $taxonomy, array $args = [])
    {
        $slug = $args['slug'] ?? Str::slug($term);
        $baseSlug = $slug;
        $i = 2;

        while (Term::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$i}";
            $i++;
        }

        $termModel = Term::create([
            'name' => $term,
            'slug' => $slug,
        ]);

        $taxonomyModel = TermTaxonomy::create([
            'term_id' => $termModel->term_id,
            'taxonomy' => $taxonomy,
            'description' => $args['description'] ?? '',
            'parent' => $args['parent'] ?? 0,
        ]);

        return [$termModel, $taxonomyModel];
    }
    public function update(int $termId, string $taxonomy, array $args = [])
    {
        $term = Term::findOrFail($termId);
        $termTax = TermTaxonomy::where('term_id', $termId)
            ->where('taxonomy', $taxonomy)
            ->firstOrFail();

        if (isset($args['name'])) {
            $term->name = trim($args['name']);
        }

        if (array_key_exists('slug', $args)) {
            $newSlug = Str::slug($args['slug'] ?: $term->name);
            $baseSlug = $newSlug;
            $i = 2;
            while (
            Term::where('slug', $newSlug)
                ->where('term_id', '!=', $termId)
                ->exists()
            ) {
                $newSlug = "{$baseSlug}-{$i}";
                $i++;
            }

            $term->slug = $newSlug;
        }

        $term->save();

        $termTax->update([
            'description' => $args['description'] ?? $termTax->description,
            'parent' => $args['parent'] ?? $termTax->parent,
        ]);

        return [
            'term_id' => $term->term_id,
            'term_taxonomy_id' => $termTax->term_taxonomy_id,
            'term' => $term,
            'taxonomy' => $termTax,
        ];
    }
    public function delete(int $termId, string $taxonomy): bool
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

        $termTaxonomyIds = TermTaxonomy::whereIn('term_id', $terms)
            ->where('taxonomy', $taxonomy)
            ->pluck('term_taxonomy_id')
            ->toArray();


        if (empty($termTaxonomyIds) && !$append) {
            TermRelationship::where('object_id', $postId)
                ->whereHas('taxonomy', fn($q) => $q->where('taxonomy', $taxonomy))
                ->delete();
            return true;
        }


        if (!$append) {
            $oldRelationships = TermRelationship::where('object_id', $postId)
                ->whereHas('taxonomy', fn($q) => $q->where('taxonomy', $taxonomy))
                ->get();
            foreach ($oldRelationships as $rel) {
                $rel->taxonomy()->decrement('count');
                $rel->delete();
            }
        }

        foreach ($termTaxonomyIds as $ttId) {
            $relationship = TermRelationship::firstOrCreate([
                'object_id' => $postId,
                'term_taxonomy_id' => $ttId,
            ]);

            if ($relationship->wasRecentlyCreated) {
                TermTaxonomy::where('term_taxonomy_id', $ttId)->increment('count');
            }
        }

        return true;
    }

    public function getPostTerms(int $postId, string $taxonomy)
    {
        return Term::whereHas('taxonomies', function ($q) use ($taxonomy, $postId) {
            $q->where('taxonomy', $taxonomy)
                ->whereHas('posts', fn($q2) => $q2->where('object_id', $postId));
        })->get();
    }
}
