<?php

namespace RezaQsr\Wp2Laravel;

use RezaQsr\Wp2Laravel\Contracts\Wp2LaravelManagerInterface;
use RezaQsr\Wp2Laravel\Services\OptionService;
use RezaQsr\Wp2Laravel\Services\PostService;

class Wp2LaravelManager implements Wp2LaravelManagerInterface
{
    protected OptionService $options;
    protected PostService $posts;

    public function __construct(OptionService $options , PostService $posts)
    {
        $this->options = $options;
        $this->posts = $posts;
    }

    public function getOption(string $key, $default = null)
    {
        return $this->options->getOption($key, $default);
    }
    public function updateOption(string $key, $value): bool
    {
        return $this->options->updateOption($key, $value);
    }
    public function deleteOption(string $key): bool
    {
        return $this->options->deleteOption($key);
    }
    public function addOption(string $key, $value, string $autoload = 'yes'): bool
    {
        return $this->options->addOption($key, $value, $autoload);
    }


    public function getPost(int $id)
    {
        return $this->posts->getPost($id);
    }

    public function getPosts(array $args = [])
    {
        return $this->posts->getPosts($args);
    }

    public function insertPost(array $data)
    {
        return $this->posts->insertPost($data);
    }

    public function updatePost(int $id, array $data)
    {
        return $this->posts->updatePost($id, $data);
    }

    public function deletePost(int $id)
    {
        return $this->posts->deletePost($id);
    }

    public function getPostMeta(int $postId, string $key, $default = null)
    {
        return $this->posts->getPostMeta($postId, $key, $default);
    }

    public function updatePostMeta(int $postId, string $key, $value)
    {
        return $this->posts->updatePostMeta($postId, $key, $value);
    }

    public function deletePostMeta(int $postId, string $key)
    {
        return $this->posts->deletePostMeta($postId, $key);
    }

}
