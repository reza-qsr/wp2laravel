<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    protected $primaryKey = 'term_taxonomy_id';
    protected $table;
    protected $guarded = [];
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.term_taxonomy_table', 'wp_term_taxonomy');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'term_id');
    }
}
