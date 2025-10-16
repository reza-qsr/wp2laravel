<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    protected $table;
    protected $primaryKey = 'umeta_id';
    public $timestamps = false;
    protected $fillable = ['user_id', 'meta_key', 'meta_value'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.usermeta_table', 'wp_usermeta');
    }
}
