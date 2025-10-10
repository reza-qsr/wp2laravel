<?php

namespace RezaQsr\Wp2Laravel;

use RezaQsr\Wp2Laravel\Contracts\Wp2LaravelManagerInterface;
use RezaQsr\Wp2Laravel\Services\OptionService;
use RezaQsr\Wp2Laravel\Services\PostService;
use RezaQsr\Wp2Laravel\Services\TaxonomyService;
use RezaQsr\Wp2Laravel\Services\TermService;

class Wp2LaravelManager implements Wp2LaravelManagerInterface
{
    protected OptionService $options;
    protected PostService $posts;
    protected TermService $terms;
    protected TaxonomyService $taxonomies;


    public function __construct(OptionService $options, PostService $posts, TermService $terms , TaxonomyService $taxonomies)
    {
        $this->options = $options;
        $this->posts = $posts;
        $this->terms = $terms;
        $this->taxonomies = $taxonomies;
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
    public function hasPostMeta(int $postId, string $key)
    {
        return $this->posts->hasPostMeta($postId, $key);
    }
    public function updatePostMeta(int $postId, string $key, $value)
    {
        return $this->posts->updatePostMeta($postId, $key, $value);
    }
    public function deletePostMeta(int $postId, string $key)
    {
        return $this->posts->deletePostMeta($postId, $key);
    }

    public function getTerms(array $args = [])
    {
        return $this->terms->getTerms($args);
    }
    public function getTermBy(string $field, $value, string $taxonomy)
    {
        return $this->terms->getTermBy($field, $value, $taxonomy);
    }
    public function insertTerm(string $term, string $taxonomy, array $args = [])
    {
        return $this->terms->insertTerm($term, $taxonomy, $args);
    }
    public function updateTerm(int $termId, string $taxonomy, array $args = [])
    {
        return $this->terms->updateTerm($termId, $taxonomy, $args);
    }
    public function deleteTerm(int $termId, string $taxonomy)
    {
        return $this->terms->deleteTerm($termId, $taxonomy);
    }
    public function setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false)
    {
        return $this->terms->setPostTerms($postId, $terms, $taxonomy, $append);
    }
    public function getPostTerms(int $postId, string $taxonomy)
    {
        return $this->terms->getPostTerms($postId, $taxonomy);
    }

    public function getTaxonomy(string $taxonomy)
    {
        return $this->taxonomies->getTaxonomy($taxonomy);
    }
    public function getTaxonomies(array $args = [])
    {
        return $this->taxonomies->getTaxonomies($args);
    }

}
