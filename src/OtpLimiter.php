<?php

namespace Raymon\OtpLimiter;

use Carbon\Carbon;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;

class OtpLimiter
{
    protected string $key;

    protected string $suffix = 'otp-limiter';

    protected int $rate;

    protected CacheManager $cacheManager;

    protected array $config;

    public function __construct(CacheManager $cacheManager, Repository $config)
    {
        $this->config = $config->get('otp-limiter');
        $this->cacheManager = $cacheManager;
    }

    public function allowed(string $key): bool
    {
        $time = $this->getTime($key);

        if (is_null($time)) {
            return true;
        }

        return !($this->remained($key) > 0);
    }

    protected function getTime(string $key): ?Carbon
    {
        return $this->cacheManager->get($this->key($key));
    }

    protected function key(string $key): string
    {
        return $key . '-' . $this->suffix;
    }

    public function remained(string $key): int
    {
        if ($time = $this->getTime($key)) {
//            dd($time->diffInSeconds(now()));
            return $time->diffInSeconds(now());
        }

        return 0;
    }

    public function set(string $key): bool
    {
        $time = now()->addSeconds($this->rate());
        return $this->cacheManager->set($this->key($key), $time, $time);
    }

    protected function rate(): int
    {
        return Arr::get($this->config, 'custom.otp-rate-limiter', 60 * 3);
    }

    public function throw(string $key)
    {
        if ($exception = $this->getException()) {
            throw new $exception($key, $this->remained($key));
        }
    }

    protected function getException(): ?string
    {
        return Arr::get($this->config, 'exception');
    }
}
