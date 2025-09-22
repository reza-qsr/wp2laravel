<?php

namespace RezaQsr\Wp2Laravel\Services;

use RezaQsr\Wp2Laravel\Contracts\OptionRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class OptionService
{
    protected OptionRepositoryInterface $repo;
    protected int $cacheTtl;

    public function __construct(OptionRepositoryInterface $repo)
    {
        $this->repo = $repo;
        $this->cacheTtl = (int) config('wp2laravel.option_cache_ttl', 0);
    }

    public function getOption(string $key, $default = null)
    {
        if ($this->cacheTtl > 0) {
            return Cache::remember("wp2l_option:{$key}", $this->cacheTtl, fn() => $this->repo->get($key, $default));
        }

        return $this->repo->get($key, $default);
    }

    public function updateOption(string $key, $value): bool
    {
        $res = $this->repo->set($key, $value);
        if ($res && $this->cacheTtl > 0) {
            Cache::put("wp2l_option:{$key}", $value, $this->cacheTtl);
        }
        return $res;
    }

    public function deleteOption(string $key): bool
    {
        $res = $this->repo->delete($key);
        if ($res && $this->cacheTtl > 0) {
            Cache::forget("wp2l_option:{$key}");
        }
        return $res;
    }

    public function addOption(string $key, $value, string $autoload = 'yes'): bool
    {
        $res = $this->repo->add($key, $value, $autoload);
        if ($res && $this->cacheTtl > 0) {
            Cache::put("wp2l_option:{$key}", $value, $this->cacheTtl);
        }
        return $res;
    }
}
