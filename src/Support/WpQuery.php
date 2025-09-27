<?php
// src/Support/WpQuery.php
namespace RezaQsr\Wp2Laravel\Support;

use RezaQsr\Wp2Laravel\Models\Post;

class WpQuery
{
    protected $query;
    protected $args = [];

    public function __construct(array $args = [])
    {
        $this->args = $args;
        $this->query = Post::query()->with(['metas', 'terms']);
        $this->applyArgs();
    }

    protected function applyArgs()
    {

        if (!empty($this->args['post_type'])) {
            $this->query->where('post_type', $this->args['post_type']);
        }


        if (!empty($this->args['post_status'])) {
            $this->query->where('post_status', $this->args['post_status']);
        }


        if (!empty($this->args['tax_query'])) {
            $this->applyTaxQuery($this->args['tax_query']);
        }


        if (!empty($this->args['meta_query'])) {
            $this->applyMetaQuery($this->args['meta_query']);
        }

        if (!empty($this->args['offset'])) {
            $this->query->offset($this->args['offset']);
        }
    }

    protected function applyTaxQuery(array $taxQueries)
    {
        $this->query->whereHas('terms.taxonomies', function ($q) use ($taxQueries) {
            foreach ($taxQueries as $tax) {
                $q->where('taxonomy', $tax['taxonomy'])
                    ->whereIn('term_id', (array) $tax['terms']);
            }
        });
    }

    protected function applyMetaQuery(array $metaQueries)
    {
        $this->query->whereHas('metas', function ($q) use ($metaQueries) {
            foreach ($metaQueries as $meta) {
                $compare = $meta['compare'] ?? '=';
                $q->where('meta_key', $meta['key'])
                    ->where('meta_value', $compare, $meta['value']);
            }
        });
    }
}
