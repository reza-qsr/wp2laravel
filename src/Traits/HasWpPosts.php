<?php

namespace RezaQsr\Wp2Laravel\Traits;

use RezaQsr\Wp2Laravel\Services\PostService;

trait HasWpPosts
{
    protected function postsService(): PostService
    {
        return app(PostService::class);
    }
    public function get_post(int $id)
    {
        return $this->postsService()->getPost($id);
    }
    public function get_posts(array $args = [])
    {
        return $this->postsService()->getPosts($args);
    }
    public function insert_post(array $data)
    {
        return $this->postsService()->insertPost($data);
    }
    public function update_post(int $id, array $data): bool
    {
        return $this->postsService()->updatePost($id, $data);
    }
    public function delete_post(int $id): bool
    {
        return $this->postsService()->deletePost($id);
    }
    public function get_post_meta(int $postId, string $key, $default = null)
    {
        return $this->postsService()->getPostMeta($postId, $key, $default);
    }
    public function has_post_meta(int $postId, string $key): bool
    {
        return $this->postsService()->hasPostMeta($postId, $key);
    }
    public function update_post_meta(int $postId, string $key, $value): bool
    {
        return $this->postsService()->updatePostMeta($postId, $key, $value);
    }
    public function delete_post_meta(int $postId, string $key): bool
    {
        return $this->postsService()->deletePostMeta($postId, $key);
    }
}
