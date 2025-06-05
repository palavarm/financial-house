<?php

namespace App\Packages\RpdPayment\Views\Admin;

use App\Packages\RpdPayment\Facades\ReportingApiService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class TransactionDetails extends Component
{
    const VIEW = 'themes::admin.default.views.app.transactions.details';

    public string $transactionId;
    public array $transaction = [];
    public bool $isLoading = false;
    private ?Carbon $lastUpdated = null;

    public function mount(string $transactionId): void
    {
        $this->transactionId = $transactionId;
        $this->loadTransaction();
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['fromDate', 'toDate'])) {
            $this->loadTransaction();
        }
    }

    public function loadTransaction(): void
    {
        $now = now();
        if ($this->lastUpdated && $now->diffInMilliseconds($this->lastUpdated) < 100) {
            return; // debounce threshold
        }

        $this->lastUpdated = $now;
        $this->isLoading = true;

        $response = ReportingApiService::getTransaction($this->transactionId);

        $this->transaction = $response ?? [];

        $this->isLoading = false;
    }

    public function render()
    {
        return view(self::VIEW, [
            'title' => 'Transaction Details - #' . $this->transactionId,
            'transaction' => $this->transaction,
        ])->layout('themes::admin.default.views.layout');
    }
}
