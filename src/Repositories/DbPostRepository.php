<?php

namespace RezaQsr\Wp2Laravel\Repositories;

use RezaQsr\Wp2Laravel\Contracts\PostRepositoryInterface;
use RezaQsr\Wp2Laravel\Models\Post;
use RezaQsr\Wp2Laravel\Models\PostMeta;
use RezaQsr\Wp2Laravel\Models\TermRelationship;
use RezaQsr\Wp2Laravel\Models\TermTaxonomy;
use Illuminate\Database\Eloquent\Builder;

class DbPostRepository implements PostRepositoryInterface
{
    public function find(int $id)
    {
        return Post::find($id);
    }

    public function query(array $args = [])
    {
        $q = Post::with(['metas', 'termTaxonomies']);

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

        if (!empty($args['orderby'])) {
            $order = $args['order'] ?? 'desc';
            $q->orderBy($args['orderby'], $order);
        } else {
            $q->orderBy('post_date', 'desc');
        }

        if (!empty($args['offset'])) {
            $q->offset((int) $args['offset']);
        }

        if (!empty($args['meta_query']) && is_array($args['meta_query'])) {
            $metaQuery = $args['meta_query'];
            $relation = strtoupper($metaQuery['relation'] ?? 'AND');

            if ($relation === 'OR') {
                $q->whereHas('metas', function (Builder $mq) use ($metaQuery) {
                    $conds = array_filter($metaQuery, function ($k) {
                        return $k !== 'relation';
                    }, ARRAY_FILTER_USE_KEY);
                    $mq->where(function (Builder $inner) use ($conds) {
                        $first = true;
                        foreach ($conds as $cond) {
                            $key = $cond['key'] ?? null;
                            $value = $cond['value'] ?? null;
                            $compare = strtoupper($cond['compare'] ?? '=');
                            if ($first) {
                                $this->applyMetaCondition($inner, $key, $compare, $value, false);
                                $first = false;
                            } else {
                                $this->applyMetaCondition($inner, $key, $compare, $value, true);
                            }
                        }
                    });
                });
            } else {
                foreach ($metaQuery as $cond) {
                    if ($cond === 'relation') continue;
                    $key = $cond['key'] ?? null;
                    $value = $cond['value'] ?? null;
                    $compare = strtoupper($cond['compare'] ?? '=');
                    $q->whereHas('metas', function (Builder $mq) use ($key, $compare, $value) {
                        $this->applyMetaCondition($mq, $key, $compare, $value, false);
                    });
                }
            }
        }

        if (!empty($args['tax_query']) && is_array($args['tax_query'])) {
            $taxQuery = $args['tax_query'];
            $relation = strtoupper($taxQuery['relation'] ?? 'AND');
            $conds = array_filter($taxQuery, function ($k) {
                return $k !== 'relation';
            }, ARRAY_FILTER_USE_KEY);

            if ($relation === 'OR') {
                $q->whereHas('termTaxonomies', function (Builder $tq) use ($conds) {
                    $first = true;
                    foreach ($conds as $cond) {
                        $taxonomy = $cond['taxonomy'] ?? null;
                        $terms = (array) ($cond['terms'] ?? []);
                        $field = $cond['field'] ?? 'term_id'; // پیش‌فرض مثل وردپرس term_id

                        $callback = function (Builder $inner) use ($taxonomy, $terms, $field) {
                            $inner->where('taxonomy', $taxonomy)
                                ->whereHas('term', function (Builder $qt) use ($terms, $field) {
                                    $qt->whereIn($field, $terms);
                                });
                        };

                        if ($first) {
                            $tq->where($callback);
                            $first = false;
                        } else {
                            $tq->orWhere($callback);
                        }
                    }
                });
            } else {
                foreach ($conds as $cond) {
                    $taxonomy = $cond['taxonomy'] ?? null;
                    $terms = (array) ($cond['terms'] ?? []);
                    $field = $cond['field'] ?? 'term_id';

                    $q->whereHas('termTaxonomies', function (Builder $tq) use ($taxonomy, $terms, $field) {
                        $tq->where('taxonomy', $taxonomy)
                            ->whereHas('term', function (Builder $qt) use ($terms, $field) {
                                $qt->whereIn($field, $terms);
                            });
                    });
                }
            }
        }


        return $q->get();
    }

    public function insert(array $data)
    {
        $defaults = [
            'post_author'        => 0,
            'post_date'          => now(),
            'post_date_gmt'      => now()->setTimezone('UTC'),
            'post_content'       => '',
            'post_title'         => '',
            'post_excerpt'       => '',
            'post_status'        => 'draft',
            'comment_status'     => 'open',
            'ping_status'        => 'open',
            'post_password'      => '',
            'post_name'          => '',
            'to_ping'            => '',
            'pinged'             => '',
            'post_modified'      => now(),
            'post_modified_gmt'  => now()->setTimezone('UTC'),
            'post_content_filtered' => '',
            'post_parent'        => 0,
            'guid'               => '',
            'menu_order'         => 0,
            'post_type'          => 'post',
            'post_mime_type'     => '',
            'comment_count'      => 0,
        ];

        $postData = array_merge($defaults, $data);

        return Post::create($postData);
    }

    public function update(int $id, array $data): bool
    {
        $post = Post::find($id);
        if (!$post) return false;
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

    public function updateMeta(int $postId, string $key, $value): bool
    {
        $serialized = $this->maybeSerialize($value);

        $meta = PostMeta::where('post_id', $postId)
            ->where('meta_key', $key)
            ->first();

        if ($meta) {
            $meta->meta_value = $serialized;
            return $meta->save();
        }

        return (bool) PostMeta::create([
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
        return (string) $value;
    }
    protected function maybeUnserialize($value)
    {
        $maybe = @unserialize($value);
        if ($maybe === false && $value !== 'b:0;') {
            return $value;
        }
        return $maybe;
    }
    protected function applyMetaCondition(Builder $mq, $key, $compare, $value, $useOr = false)
    {
        $method = $useOr ? 'orWhere' : 'where';

        switch ($compare) {
            case 'IN':
                $mq->{$method}('meta_key', $key);
                $mq->{$method . 'In'}('meta_value', (array) $value);
                $mq->where('meta_key', $key)->whereIn('meta_value', (array) $value);
                break;

            case 'LIKE':
                if ($useOr) {
                    $mq->orWhere(function (Builder $q) use ($key, $value) {
                        $q->where('meta_key', $key)
                            ->where('meta_value', 'LIKE', "%{$value}%");
                    });
                } else {
                    $mq->where(function (Builder $q) use ($key, $value) {
                        $q->where('meta_key', $key)
                            ->where('meta_value', 'LIKE', "%{$value}%");
                    });
                }
                break;

            default:
                if ($useOr) {
                    $mq->orWhere(function (Builder $q) use ($key, $compare, $value) {
                        $q->where('meta_key', $key)
                            ->where('meta_value', $compare, $value);
                    });
                } else {
                    $mq->where(function (Builder $q) use ($key, $compare, $value) {
                        $q->where('meta_key', $key)
                            ->where('meta_value', $compare, $value);
                    });
                }
        }
    }
}
