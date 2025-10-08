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
        $this->table = config('wp2laravel.terms_table', 'wp_terms');
    }


    protected static function booted(): void
    {
        static::updating(function (Term $term) {
            $slug = $term->slug ?: Str::slug($term->name);

            $baseSlug = $slug;
            $i = 2;
            while (Term::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }
            $term->slug = $slug;
        });
    }

    public function taxonomies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TermTaxonomy::class, 'term_id', 'term_id');
    }
}
