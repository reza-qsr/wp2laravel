<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class TermRelationship extends Model
{
    protected $table;
    protected $guarded = [];
    public $timestamps = false;
    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.term_relationships_table', 'wp_term_relationships');
    }

    public function post(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Post::class, 'object_id');
    }

    public function taxonomy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TermTaxonomy::class, 'term_taxonomy_id');
    }
}
