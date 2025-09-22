<?php

namespace RezaQsr\Wp2Laravel\Repositories;
// namespace

use RezaQsr\Wp2Laravel\Contracts\OptionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DbOptionRepository implements OptionRepositoryInterface
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('wp2laravel.options_table', 'wp_options');
    }

    public function get(string $key, $default = null)
    {
        $row = DB::table($this->table)->where('option_name', $key)->first();

        if (!$row) {
            return $default;
        }

        return $this->maybeUnserialize($row->option_value);
    }

    public function set(string $key, $value): bool
    {
        $valueToStore = $this->maybeSerialize($value);

        $exists = DB::table($this->table)->where('option_name', $key)->exists();

        if ($exists) {
            return (bool)DB::table($this->table)->where('option_name', $key)->update(['option_value' => $valueToStore]);
        }
        return (bool)DB::table($this->table)->insert(['option_name' => $key, 'option_value' => $valueToStore, 'autoload' => 'yes']);
    }

    public function delete(string $key): bool
    {
        return (bool)DB::table($this->table)->where('option_name', $key)->delete();
    }

    public function add(string $key, $value, string $autoload = 'yes'): bool
    {
        $exists = DB::table($this->table)->where('option_name', $key)->exists();
        if ($exists) return false;
        $valueToStore = $this->maybeSerialize($value);

        return (bool)DB::table($this->table)->insert(['option_name' => $key, 'option_value' => $valueToStore, 'autoload' => $autoload]);
    }

    protected function maybeSerialize($value)
    {
        if (is_array($value) || is_object($value)) {
            return serialize($value);
        }
        return (string)$value;
    }

    protected function maybeUnserialize($value)
    {

        $maybe = @unserialize($value);
        if ($maybe === false && $value !== 'b:0;') {
            return $value;
        }
        return $maybe;
    }
}
