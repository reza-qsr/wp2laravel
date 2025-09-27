<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $table;
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.terms_table', 'wp_terms');
    }

    public function taxonomies()
    {
        return $this->hasMany(TermTaxonomy::class, 'term_id');
    }
}
