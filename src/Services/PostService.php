<?php

namespace RezaQsr\Wp2Laravel\Services;

use RezaQsr\Wp2Laravel\Contracts\PostRepositoryInterface;

class PostService
{
    protected PostRepositoryInterface $repo;

    public function __construct(PostRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getPost(int $id)
    {
        return $this->repo->find($id);
    }

    public function getPosts(array $args = [])
    {
        return $this->repo->query($args);
    }

    public function insertPost(array $data)
    {
        return $this->repo->insert($data);
    }

    public function updatePost(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function deletePost(int $id): bool
    {
        return $this->repo->delete($id);
    }


    public function getPostMeta(int $postId, string $key, $default = null)
    {
        return $this->repo->getMeta($postId, $key, $default);
    }
    public function hasPostMeta(int $postId, string $key): bool
    {
        return $this->repo->hasMeta($postId, $key);
    }
    public function updatePostMeta(int $postId, string $key, $value): bool
    {
        return $this->repo->updateMeta($postId, $key, $value);
    }

    public function deletePostMeta(int $postId, string $key): bool
    {
        return $this->repo->deleteMeta($postId, $key);
    }
}
