<?php

namespace RezaQsr\Wp2Laravel\Traits;

use RezaQsr\Wp2Laravel\Services\OptionService;

trait HasWpOptions
{

    protected function optionsService(): OptionService
    {
        return app(OptionService::class);
    }


    public function get_option(string $key, $default = null)
    {
        return $this->optionsService()->getOption($key, $default);
    }

    public function update_option(string $key, $value): bool
    {
        return $this->optionsService()->updateOption($key, $value);
    }

    public function delete_option(string $key): bool
    {
        return $this->optionsService()->deleteOption($key);
    }

    public function add_option(string $key, $value, string $autoload = 'yes'): bool
    {
        return $this->optionsService()->addOption($key, $value, $autoload);
    }
}
