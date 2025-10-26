<?php

namespace RezaQsr\Wp2Laravel\Traits;

use RezaQsr\Wp2Laravel\Services\TermService;

trait HasWpTerms
{
    protected function termsService(): TermService
    {
        return app(TermService::class);
    }

    public function get_terms(array $args = [])
    {
        return $this->termsService()->getTerms($args);
    }
    public function get_term_by(string $field, $value, string $taxonomy)
    {
        return $this->termsService()->getTermBy($field, $value, $taxonomy)();
    }
    public function insert_term(string $term, string $taxonomy, array $args = [])
    {
        return $this->termsService()->insertTerm($term, $taxonomy, $args);
    }
    public function update_term(int $termId, string $taxonomy, array $args = [])
    {
        return $this->termsService()->updateTerm($termId, $taxonomy, $args);
    }
    public function delete_term(int $termId, string $taxonomy)
    {
        return $this->termsService()->deleteTerm($termId, $taxonomy);
    }
    public function set_post_terms(int $postId, array $terms, string $taxonomy, bool $append = false)
    {
        return $this->termsService()->setPostTerms($postId, $terms, $taxonomy, $append);
    }
    public function get_post_terms(int $postId, string $taxonomy)
    {
        return $this->termsService()->getPostTerms($postId, $taxonomy);
    }
}
