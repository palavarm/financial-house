<?php

namespace App\Packages\RpdPayment\Views\Admin;

use App\Packages\RpdPayment\Facades\ReportingApiService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class TransactionsReport extends Component
{
    const VIEW = 'themes::admin.default.views.app.transactions.report';

    public string $fromDate;
    public string $toDate;
    public ?string $merchantId = null;
    public ?string $acquirer = null;
    public array $reports = [];
    public bool $isLoading = false;
    private ?Carbon $lastUpdated = null;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->fromDate = now()->subMonths(6)->toDateString();
        $this->toDate = now()->toDateString();
        $this->loadReports();
    }

    /**
     * @return void
     */
    public function loadReports(): void
    {
        $now = now();
        if ($this->lastUpdated && $now->diffInMilliseconds($this->lastUpdated) < 100) {
            return;
        }

        $this->lastUpdated = $now;
        $this->isLoading = true;

        $params = [
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
        ];

        if ($this->merchantId) {
            $params['merchantId'] = $this->merchantId;
        }

        if ($this->acquirer) {
            $params['acquirer'] = $this->acquirer;
        }

        $response = ReportingApiService::transactionReport($params);

        $this->reports = $response['response'] ?? [];

        $this->isLoading = false;
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view(self::VIEW, [
            'reportData' => [
                'title' => 'Transaction Report',
                'reports' => $this->reports,
            ]
        ])->layout('themes::admin.default.views.layout');
    }
}
