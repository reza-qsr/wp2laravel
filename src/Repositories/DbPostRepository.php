<?php

namespace RezaQsr\Wp2Laravel\Repositories;

use RezaQsr\Wp2Laravel\Contracts\PostRepositoryInterface;
use RezaQsr\Wp2Laravel\Models\Post;
use RezaQsr\Wp2Laravel\Models\PostMeta;
use RezaQsr\Wp2Laravel\Models\TermRelationship;
use RezaQsr\Wp2Laravel\Support\Sanitizer;

class DbPostRepository implements PostRepositoryInterface
{
    public function find(int $id)
    {
        return Post::query()->find($id);
    }

    public function query(array $args = []): \Illuminate\Database\Eloquent\Collection
    {
        $q = Post::with(['meta', 'taxonomies']);

        if (!empty($args['post_type'])) {
            $q->where('post_type', $args['post_type']);
        }

        if (!empty($args['post_status'])) {
            $q->where('post_status', $args['post_status']);
        }

        if (!empty($args['include']) && is_array($args['include'])) {
            $q->whereIn('ID', $args['include']);
        }

        if (!empty($args['exclude']) && is_array($args['exclude'])) {
            $q->whereNotIn('ID', $args['exclude']);
        }

        if (!empty($args['author'])) {
            $q->where('post_author', $args['author']);
        }

        if (!empty($args['meta_query'])) {
            $this->applyMetaQuery($q, $args['meta_query']);
        }

        if (!empty($args['tax_query'])) {
            $this->applyTaxQuery($q, $args['tax_query']);
        }


        return $q->with('meta')->get();
    }

    public function insert(array $data)
    {
        $now = now();
        $nowGmt = $now->clone()->setTimezone('UTC');

        $defaults = [
            'post_author' => 0,
            'post_date' => $now,
            'post_date_gmt' => $nowGmt,
            'post_content' => '',
            'post_title' => 'Draft',
            'post_excerpt' => '',
            'post_status' => 'draft',
            'comment_status' => 'open',
            'ping_status' => 'open',
            'post_password' => '',
            'post_name' => '',
            'to_ping' => '',
            'pinged' => '',
            'post_modified' => $now,
            'post_modified_gmt' => $nowGmt,
            'post_content_filtered' => '',
            'post_parent' => 0,
            'guid' => '',
            'menu_order' => 0,
            'post_type' => 'post',
            'post_mime_type' => '',
            'comment_count' => 0,
        ];


        $postData = array_merge($defaults, $data);


        if (isset($postData['post_title'])) {
            $postData['post_title'] = Sanitizer::text($postData['post_title']);
        }
        if (empty($postData['post_name']) && !empty($postData['post_title'])) {
            $postData['post_name'] = Sanitizer::slug($postData['post_title']);
        } else if (isset($postData['post_name'])) {
            $postData['post_name'] = Sanitizer::slug($postData['post_name']);
        }

        return Post::create($postData);
    }

    public function update(int $id, array $data): bool
    {
        $post = Post::find($id);
        if (!$post) {
            return false;
        }

        $now = now();
        $nowGmt = $now->clone()->setTimezone('UTC');

        $data['post_modified'] = $data['post_modified'] ?? $now;
        $data['post_modified_gmt'] = $data['post_modified_gmt'] ?? $nowGmt;
        if (isset($data['post_title'])) {
            $data['post_title'] = Sanitizer::text($data['post_title']);
        }
        if (isset($data['post_title']) && empty($data['post_name'])) {
            $data['post_name'] = Sanitizer::slug($data['post_title']);
        } else if (isset($data['post_name'])) {
            $data['post_name'] = Sanitizer::slug($data['post_name']);
        }
        return $post->update($data);
    }

    public function delete(int $id): bool
    {
        PostMeta::where('post_id', $id)->delete();
        TermRelationship::where('object_id', $id)->delete();
        return Post::where('ID', $id)->delete() > 0;
    }

    public function getMeta(int $postId, string $key, $default = null)
    {
        $meta = PostMeta::where('post_id', $postId)
            ->where('meta_key', $key)
            ->first();

        if (!$meta) return $default;

        return $this->maybeUnserialize($meta->meta_value);
    }

    public function hasMeta(int $postId, string $key): bool
    {
        return PostMeta::where('post_id', $postId)
            ->where('meta_key', $key)
            ->exists();
    }

    public function updateMeta(int $postId, string $key, $value): bool
    {
        $value = Sanitizer::value($value);
        $serialized = $this->maybeSerialize($value);

        $meta = PostMeta::where('post_id', $postId)
            ->where('meta_key', $key)
            ->first();

        if ($meta) {
            $meta->meta_value = $serialized;
            return $meta->save();
        }

        return (bool)PostMeta::create([
            'post_id' => $postId,
            'meta_key' => $key,
            'meta_value' => $serialized,
        ]);
    }

    public function deleteMeta(int $postId, string $key): bool
    {
        return PostMeta::where('post_id', $postId)
                ->where('meta_key', $key)
                ->delete() > 0;
    }

    protected function maybeSerialize($value)
    {
        if (is_array($value) || is_object($value)) {
            return serialize($value);
        }
        return (string)$value;
    }

    protected function maybeUnserialize($value)
    {
        $maybe = @unserialize($value);
        if ($maybe === false && $value !== 'b:0;') {
            return $value;
        }
        return $maybe;
    }

    protected function applyMetaQuery($query, array $metaQuery): void
    {
        $relation = strtoupper($metaQuery['relation'] ?? 'AND');
        $clauses = $metaQuery;
        unset($clauses['relation']);

        $query->where(function ($q) use ($clauses, $relation) {
            foreach ($clauses as $clause) {
                if (isset($clause['relation'])) {
                    $this->applyMetaQuery($q, $clause);
                    continue;
                }

                $key = $clause['key'] ?? null;
                $value = $clause['value'] ?? null;
                $compare = strtoupper($clause['compare'] ?? '=');
                $type = strtoupper($clause['type'] ?? 'CHAR');

                $method = $relation === 'AND' ? 'whereHas' : 'orWhereHas';

                $q->{$method}('meta', function ($meta) use ($key, $value, $compare, $type) {
                    if ($key) {
                        $meta->where('meta_key', $key);
                    }


                    if ($compare === 'EXISTS') {
                        $meta->whereNotNull('meta_value');
                        return;
                    }
                    if ($compare === 'NOT EXISTS') {
                        $meta->whereNull('meta_value');
                        return;
                    }

                    if ($value !== null) {
                        switch ($compare) {
                            case 'IN':
                                $meta->whereIn('meta_value', (array)$value);
                                break;
                            case 'NOT IN':
                                $meta->whereNotIn('meta_value', (array)$value);
                                break;
                            case 'BETWEEN':
                                $meta->whereBetween('meta_value', (array)$value);
                                break;
                            case 'NOT BETWEEN':
                                $meta->whereNotBetween('meta_value', (array)$value);
                                break;
                            case 'LIKE':
                            case 'NOT LIKE':
                                $operator = $compare === 'LIKE' ? 'LIKE' : 'NOT LIKE';
                                $meta->where('meta_value', $operator, "%{$value}%");
                                break;
                            default:
                                if ($type === 'NUMERIC') {
                                    $meta->whereRaw("CAST(meta_value AS SIGNED) {$compare} ?", [$value]);
                                } else {
                                    $meta->where('meta_value', $compare, $value);
                                }
                                break;
                        }
                    }
                });
            }
        });
    }

    protected function applyTaxQuery($query, array $taxQuery): void
    {
        $relation = strtoupper($taxQuery['relation'] ?? 'AND');
        $clauses = $taxQuery;
        unset($clauses['relation']);

        $query->where(function ($q) use ($clauses, $relation) {
            foreach ($clauses as $clause) {


                if (isset($clause['relation'])) {
                    $this->applyTaxQuery($q, $clause);
                    continue;
                }

                $taxonomy = $clause['taxonomy'] ?? null;
                $terms = (array)($clause['terms'] ?? []);
                $operator = strtoupper($clause['operator'] ?? 'IN');
                $field = strtolower($clause['field'] ?? 'id');

                $column = match ($field) {
                    'slug' => 'slug',
                    'name' => 'name',
                    default => 'term_id'
                };

                $method = $relation === 'AND' ? 'whereHas' : 'orWhereHas';

                $q->{$method}('taxonomies', function ($tax) use ($taxonomy, $terms, $operator, $column) {
                    if ($taxonomy) {
                        $tax->where('taxonomy', $taxonomy);
                    }

                    $tax->whereHas('term', function ($termQuery) use ($column, $terms, $operator) {
                        switch ($operator) {
                            case 'IN':
                                $termQuery->whereIn($column, $terms);
                                break;
                            case 'NOT IN':
                                $termQuery->whereNotIn($column, $terms);
                                break;
                            case 'AND':
                                foreach ($terms as $term) {
                                    $termQuery->where($column, $term);
                                }
                                break;
                        }
                    });
                });


                if ($operator === 'EXISTS') {
                    $q->has('taxonomies');
                }
                if ($operator === 'NOT EXISTS') {
                    $q->doesntHave('taxonomies');
                }
            }
        });
    }


}
