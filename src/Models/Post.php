<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table;
    protected $guarded = [];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.posts_table', 'wp_posts');
    }

    public function metas()
    {
        return $this->hasMany(PostMeta::class, 'post_id');
    }

    public function terms()
    {
        return $this->belongsToMany(
            Term::class,
            config('wp2laravel.term_relationships'),
            'object_id',
            'term_taxonomy_id'
        )->withPivot('term_taxonomy_id');
    }
}
