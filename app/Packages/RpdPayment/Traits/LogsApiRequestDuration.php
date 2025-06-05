<?php

namespace App\Packages\RpdPayment\Traits;

use Illuminate\Support\Facades\Log;
use RuntimeException;

trait LogsApiRequestDuration
{
    /**
     * @param string $label
     * @param callable $callback
     * @param int $warnThresholdMs
     * @return mixed
     */
    public function requestWithTiming(string $label, callable $callback, int $warnThresholdMs = 1000): mixed
    {
        $start = microtime(true);
        $userId = optional(auth()->user())->id ?? 'guest';
        $userEmail = optional(auth()->user())->email ?? 'unauthenticated';

        try {
            $response = $callback();

            $duration = round((microtime(true) - $start) * 1000, 2);
            Log::info("API call '{$label}' by user {$userId} ({$userEmail}) in {$duration}ms");

            if ($duration >= $warnThresholdMs) {
                Log::channel('slack')->warning("*SLOW API* '{$label}' ({$duration}ms) by user {$userId}");
            }

            return $response;
        } catch (\Throwable $exception) {
            $duration = round((microtime(true) - $start) * 1000, 2);

            Log::error("API call '{$label}' failed after {$duration}ms by user {$userId}", [
                'message' => $exception->getMessage(),
                'user_id' => $userId,
                'user_email' => $userEmail,
                'trace' => $exception->getTraceAsString(),
            ]);

            Log::channel('slack')->error("*API FAILURE* '{$label}' ({$duration}ms) by user {$userId}: {$exception->getMessage()}");

            throw new RuntimeException("[{$label}] API failed: " . $exception->getMessage(), 500, $exception);
        }
    }
}
