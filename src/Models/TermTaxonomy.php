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
        $this->table = config('wp2laravel.term_taxonomy_table', 'wp_term_taxonomy');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($taxonomy) {
            $existing = static::where('term_id', $taxonomy->term_id)
                ->where('taxonomy', $taxonomy->taxonomy)
                ->first();

            if ($existing) {
                $taxonomy->exists = true;
                $taxonomy->term_taxonomy_id = $existing->term_taxonomy_id;
                return false;
            }
        });
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id', 'term_id');
    }
}
