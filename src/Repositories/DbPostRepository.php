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




        return $q->get();
    }

    public function insert(array $data)
    {
        if (empty($data['post_date'])) {
            $data['post_date'] = now();
        }
        return Post::create($data);
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

}
