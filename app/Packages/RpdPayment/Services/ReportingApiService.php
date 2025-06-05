<?php

namespace App\Packages\RpdPayment\Services;

use App\Packages\RpdPayment\Traits\LogsApiRequestDuration;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ReportingApiService
{
    use LogsApiRequestDuration;

    protected string $baseUrl;
    protected string $email;
    protected string $password;
    protected int $tokenExpireDuration;

    public function __construct()
    {
        $this->baseUrl = config('rpd_payment.base_url');
        $this->email = config('rpd_payment.email');
        $this->password = config('rpd_payment.password');
        $this->tokenExpireDuration = config('rpd_payment.token_expire_duration');
    }

    /**
     * @return string
     */
    protected function getToken(): string
    {
        return Cache::remember('reporting_api_token', now()->addMinutes($this->tokenExpireDuration), function () {
            $response = Http::post("{$this->baseUrl}/merchant/user/login", [
                'email' => $this->email,
                'password' => $this->password,
            ]);

            if (! $response->ok() || !isset($response['token'])) {
                throw new \Exception('Failed to authenticate with Reporting API');
            }

            return $response['token'];
        });
    }

    /**
     * @return PendingRequest
     */
    protected function request(): PendingRequest
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $this->getToken(),
        ])
            ->timeout(10)
            ->retry(3, 200, function ($exception, $request) {
                return $exception instanceof RequestException
                    || ($exception->response && $exception->response->serverError());
            });
    }

    /**
     * @param array $params
     * @return array
     */
    public function transactionReport(array $params): array
    {
        return $this->requestWithTiming('Transaction Report', function () use ($params) {
            return $this->request()
                ->post("{$this->baseUrl}/transactions/report", $params)
                ->json();
        }, 1500);
    }

    /**
     * @param array $params
     * @param int|null $page
     * @return array
     */
    public function transactionList(array $params, ?int $page = null): array
    {
        $requestPath = 'transaction/list';

        if ($page) {
            $requestPath .= '?page=' . $page;
        }

        return $this->requestWithTiming('Transaction List', function () use ($params, $requestPath) {
            return $this->request()
                ->post("{$this->baseUrl}/{$requestPath}", $params)
                ->json();
        }, 1500);
    }

    /**
     * @param string $transactionId
     * @return array
     */
    public function getTransaction(string $transactionId): array
    {
        return $this->requestWithTiming('Transaction Detail', function () use ($transactionId) {
            return $this->request()
                ->post("{$this->baseUrl}/transaction", [
                    'transactionId' => $transactionId
                ])
                ->json();
        }, 500);
    }

    /**
     * @param string $transactionId
     * @return array
     */
    public function getClient(string $transactionId): array
    {
        return $this->requestWithTiming('Client', function () use ($transactionId) {
            return $this->request()
                ->post("{$this->baseUrl}/client", [
                    'transactionId' => $transactionId
                ])
                ->json();
        }, 500);
    }
}
