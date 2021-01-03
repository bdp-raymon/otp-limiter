<?php


namespace BdpRaymon\OtpLimiter\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class OtpRequestExceededException extends Exception
{

    protected string $key;

    protected int $remainingTime;

    public function __construct($key, $remainingTime)
    {
        parent::__construct("Otp rate limit request for {$key} exceeded, for new request you should wait {$remainingTime} seconds");
        $this->key = $key;
        $this->remainingTime = $remainingTime;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->message
        ], 429)->withHeaders([
            'Retry-After' => $this->remainingTime
        ]);
    }

}
