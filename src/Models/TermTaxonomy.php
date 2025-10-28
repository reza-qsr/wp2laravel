<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    protected $primaryKey = 'term_taxonomy_id';
    protected $table;
    public $timestamps = false;

    protected $fillable = [
        'term_id',
        'taxonomy',
        'description',
        'parent',
        'count',
    ];

    protected $attributes = [
        'description' => '',
        'parent'      => 0,
        'count'       => 0,
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.tables.term_taxonomy_table', 'wp_term_taxonomy');
    }

    public function term(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id', 'term_id');
    }
    public function children()
    {
        return $this->hasMany(self::class, 'parent');
    }
    public function posts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Post::class,
            config('wp2laravel.tables.term_relationships_table', 'wp_term_relationships'),
            'term_taxonomy_id',
            'object_id');
    }
}
