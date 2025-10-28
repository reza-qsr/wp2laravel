<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{
    protected $primaryKey = 'meta_id';
    protected $table;
    public $timestamps = false;
    protected $fillable = [
        'post_id',
        'meta_key',
        'meta_value',
    ];
    protected $attributes = [
        'meta_value' => '',
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.tables.postmeta_table', 'wp_postmeta');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'ID');
    }
}
