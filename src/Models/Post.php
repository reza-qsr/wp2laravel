<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $primaryKey = 'ID';
    protected $table;
    protected $guarded = [];
    public $timestamps = false;


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.tables.posts_table', 'wp_posts');
    }

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            $slug = $post->post_name ?: Str::slug($post->post_title);

            $baseSlug = $slug;
            $i = 2;
            while (Post::where('post_name', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }
            $post->post_name = $slug;
        });
    }

    public function meta(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostMeta::class, 'post_id', $this->primaryKey);
    }

    public function taxonomies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            TermTaxonomy::class,
            config('wp2laravel.tables.term_relationships_table', 'wp_term_relationships'),
            'object_id',
            'term_taxonomy_id'
        );
    }

    public function terms(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Term::class,
            TermTaxonomy::class,
            'term_taxonomy_id',
            'term_id',
            'ID',
            'term_id'
        );
    }
}
