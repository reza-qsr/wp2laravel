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

        if (array_key_exists('taxonomy', $args) && !empty($args['taxonomy'])) {
            $query->whereHas('taxonomies', function ($q) use ($args) {
                $q->whereIn('taxonomy', (array)$args['taxonomy']);
            });
        }

        if (!empty($args['slug'])) {
            $query->whereIn('slug', (array)$args['slug']);
        }

        if (!empty($args['search'])) {
            $query->where('name', 'like', '%' . $args['search'] . '%');
        }

        return $query->get();
    }

    public function getBy(string $field, $value, string $taxonomy)
    {
        $allowed = ['id', 'slug', 'name', 'term_taxonomy_id'];
        if (!in_array($field, $allowed)) {
            throw new \InvalidArgumentException("Invalid field '{$field}' for getTermBy.");
        }

        $query = Term::query()->with('taxonomies');

        switch ($field) {
            case 'id':
                $query->where('term_id', $value);
                break;
            case 'slug':
                $query->where('slug', $value);
                break;
            case 'name':
                $query->where('name', $value);
                break;
            case 'term_taxonomy_id':
                $query->whereHas('taxonomies', function ($q) use ($value) {
                    $q->where('term_taxonomy_id', $value);
                });
                break;
        }

        if (!empty($taxonomy)) {
            $query->whereHas('taxonomies', function ($q) use ($taxonomy) {
                $q->where('taxonomy', $taxonomy);
            });
        }
        return $query->get();
    }

    public function insert(string $term, string $taxonomy, array $args = [])
    {
        $name = trim($term);
        $slug = isset($args['slug']) ? Str::slug($args['slug']) : Str::slug($name);
        $description = $args['description'] ?? '';
        $parent = $args['parent'] ?? 0;

        $baseSlug = $slug;
        $i = 2;
        while (Term::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$i}";
            $i++;
        }

        $termModel = Term::create([
            'name' => $name,
            'slug' => $slug,
        ]);

        $taxonomyModel = TermTaxonomy::create([
            'term_id' => $termModel->term_id,
            'taxonomy' => $taxonomy,
            'description' => $description,
            'parent' => $parent,
            'count' => 0,
        ]);

        return (object) [
            'term' => $termModel,
            'taxonomy' => $taxonomyModel,
        ];
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
            while (Term::where('slug', $newSlug)->where('term_id', '!=', $termId)->exists()) {
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

        return (object) [
            'term' => $term,
            'taxonomy' => $termTax,
        ];
    }

    public function delete(int $termId, string $taxonomy): bool
    {
        $term = Term::find($termId);
        if (!$term) {
            return false;
        }

        $termTax = TermTaxonomy::where('term_id', $termId)
            ->where('taxonomy', $taxonomy)
            ->first();

        if ($termTax) {
            TermRelationship::where('term_taxonomy_id', $termTax->term_taxonomy_id)->delete();
            $termTax->delete();
        }

        $remaining = TermTaxonomy::where('term_id', $termId)->count();
        if ($remaining === 0) {
            $term->delete();
        }

        return true;
    }

    public function setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false)
    {
        $terms = array_unique(array_filter($terms, 'is_numeric'));
        if (empty($terms)) {
            if (!$append) {
                TermRelationship::where('object_id', $postId)
                    ->whereHas('taxonomy', fn($q) => $q->where('taxonomy', $taxonomy))
                    ->delete();
            }
            return true;
        }

        $validTermIds = Term::whereIn('term_id', $terms)->pluck('term_id')->toArray();

        if (empty($validTermIds)) {
            if (!$append) {
                TermRelationship::where('object_id', $postId)
                    ->whereHas('taxonomy', fn($q) => $q->where('taxonomy', $taxonomy))
                    ->delete();
            }
            return true;
        }

        $termTaxonomyIds = TermTaxonomy::whereIn('term_id', $validTermIds)
            ->where('taxonomy', $taxonomy)
            ->pluck('term_taxonomy_id')
            ->toArray();

        if (empty($termTaxonomyIds)) {
            if (!$append) {
                TermRelationship::where('object_id', $postId)
                    ->whereHas('taxonomy', fn($q) => $q->where('taxonomy', $taxonomy))
                    ->delete();
            }
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
