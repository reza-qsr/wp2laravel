<?php

namespace RezaQsr\Wp2Laravel\Repositories;

use RezaQsr\Wp2Laravel\Contracts\OptionRepositoryInterface;
use RezaQsr\Wp2Laravel\Models\Option;

class DbOptionRepository implements OptionRepositoryInterface
{
    public function get(string $key, $default = null)
    {
        $option = Option::where('option_name', $key)->first();
        if (!$option) {
            return $default;
        }

        return $this->maybeUnserialize($option->option_value);
    }

    public function set(string $key, $value): bool
    {
        $option = Option::where('option_name', $key)->first();

        $serializedValue = $this->maybeSerialize($value);

        if ($option) {
            $option->option_value = $serializedValue;
            return $option->save();
        }

        return (bool)Option::create([
            'option_name' => $key,
            'option_value' => $serializedValue,
            'autoload' => 'yes',
        ]);
    }

    public function delete(string $key): bool
    {
        return Option::where('option_name', $key)->delete() > 0;
    }

    public function add(string $key, $value, string $autoload = 'yes'): bool
    {
        $exists = Option::where('option_name', $key)->exists();
        if ($exists) return false;

        $serializedValue = $this->maybeSerialize($value);

        return (bool)Option::create([
            'option_name' => $key,
            'option_value' => $serializedValue,
            'autoload' => $autoload,
        ]);
    }

    protected function maybeSerialize($value)
    {
        if (is_array($value) || is_object($value)) {
            return serialize($value);
        }
        return (string) $value;
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
