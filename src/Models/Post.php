<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $primaryKey = 'ID';
    protected $table;
    protected $guarded = [];
    public $timestamps = false;


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.posts_table', 'wp_posts');
    }

    public function metas()
    {
        return $this->hasMany(PostMeta::class, 'post_id', $this->primaryKey);
    }

    public function termTaxonomies()
    {
        return $this->belongsToMany(
            TermTaxonomy::class,
            config('wp2laravel.term_relationships_table', 'wp_term_relationships'),
            'object_id',
            'term_taxonomy_id'
        );
    }
}
