<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Term extends Model
{
    protected $primaryKey = 'term_id';
    protected $table;
    protected $fillable = ['name', 'slug', 'term_group'];
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.tables.terms_table', 'wp_terms');
    }

    public function taxonomies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TermTaxonomy::class, 'term_id', 'term_id');
    }
}
