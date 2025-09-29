<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

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


    protected static function booted()
    {
        static::creating(function (Term $term) {
            $slug = $term->slug ?: Str::slug($term->name);

            $baseSlug = $slug;
            $i = 2;
            while (Term::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }
            $term->slug = $slug;
        });
    }

    public function taxonomies()
    {
        return $this->hasMany(TermTaxonomy::class, 'term_id', 'term_id');
    }
}
