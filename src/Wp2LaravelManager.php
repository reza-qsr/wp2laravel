<?php

namespace RezaQsr\Wp2Laravel;

use RezaQsr\Wp2Laravel\Services\OptionService;

class Wp2LaravelManager
{
    protected OptionService $options;

    public function __construct(OptionService $options)
    {
        $this->options = $options;
    }

    public function getOption(string $key, $default = null)
    {
        return $this->options->getOption($key, $default);
    }

    public function updateOption(string $key, $value): bool
    {
        return $this->options->updateOption($key, $value);
    }

    public function deleteOption(string $key): bool
    {
        return $this->options->deleteOption($key);
    }

    public function addOption(string $key, $value, string $autoload = 'yes'): bool
    {
        return $this->options->addOption($key, $value, $autoload);
    }
}
