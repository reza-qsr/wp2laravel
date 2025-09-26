<?php

namespace RezaQsr\Wp2Laravel\Services;

use RezaQsr\Wp2Laravel\Contracts\OptionRepositoryInterface;

class OptionService
{
    protected OptionRepositoryInterface $repo;

    public function __construct(OptionRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getOption(string $key, $default = null)
    {
        return $this->repo->get($key, $default);
    }

    public function updateOption(string $key, $value): bool
    {
        return $this->repo->set($key, $value);
    }

    public function deleteOption(string $key): bool
    {
        return $this->repo->delete($key);
    }

    public function addOption(string $key, $value, string $autoload = 'yes'): bool
    {
        return $this->repo->add($key, $value, $autoload);
    }
}
