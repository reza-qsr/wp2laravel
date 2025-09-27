<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{
    protected $table;
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.postmeta_table', 'wp_postmeta');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
