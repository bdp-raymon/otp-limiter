<?php

namespace BdpRaymon\OtpLimiter;

use Carbon\Carbon;
use Exception;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;

class OtpLimiter
{
    protected string $suffix = 'otp-limiter';

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
            return $time->diffInSeconds(now());
        }

        return 0;
    }

    public function set(string $key): bool
    {
        return $this->cacheManager->set(
            $this->key($key),
            now()->addSeconds($this->rate()),
            $this->rate()
        );
    }

    protected function rate(): int
    {
        return Arr::get($this->config, 'custom.otp-rate-limiter', 60 * 3);
    }

    /**
     * @param string $key
     * @throws Exception
     * @return void
     */
    public function throw(string $key): void
    {
        $exception = $this->getException();

        if (is_null($exception)) {
            return;
        }

        $throwable = new $exception($key, $this->remained($key));

        if ($throwable instanceof Exception) {
            throw $throwable;
        }
    }

    protected function getException(): ?string
    {
        return Arr::get($this->config, 'exception');
    }
}
