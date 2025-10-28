<?php

namespace RezaQsr\Wp2Laravel\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table;
    protected $fillable = ['option_name', 'option_value', 'autoload'];

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('wp2laravel.tables.options_table', 'wp_options');
    }
}
