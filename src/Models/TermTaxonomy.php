<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    protected $table;
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.term_taxonomy_table', 'wp_term_taxonomy');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }
}
