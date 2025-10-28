<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table;
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'user_login', 'user_pass', 'user_nicename', 'user_email',
        'user_url', 'user_registered', 'user_status', 'display_name'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.tables.users_table', 'wp_users');
    }
    public function metas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserMeta::class, 'user_id');
    }
}
