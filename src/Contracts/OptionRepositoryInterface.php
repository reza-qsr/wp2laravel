<?php

namespace RezaQsr\Wp2Laravel\Contracts;

interface OptionRepositoryInterface
{
    public function get(string $key, $default = null);
    public function set(string $key, $value): bool;
    public function delete(string $key): bool;
    public function add(string $key, $value, string $autoload = 'yes'): bool;
}
