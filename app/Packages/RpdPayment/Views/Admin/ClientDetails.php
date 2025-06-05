<?php

namespace App\Packages\RpdPayment\Views\Admin;

use App\Packages\RpdPayment\Facades\ReportingApiService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ClientDetails extends Component
{
    const VIEW = 'themes::admin.default.views.app.client.details';

    public string $transactionId;
    public array $client = [];
    public bool $isLoading = false;
    private ?Carbon $lastUpdated = null;

    public function mount(string $transactionId): void
    {
        $this->transactionId = $transactionId;
        $this->loadClient();
    }

    public function loadClient(): void
    {
        $now = now();
        if ($this->lastUpdated && $now->diffInMilliseconds($this->lastUpdated) < 100) {
            return; // debounce threshold
        }

        $this->lastUpdated = $now;
        $this->isLoading = true;

        $response = ReportingApiService::getClient($this->transactionId);

        $this->client = $response['customerInfo'] ?? [];

        $this->isLoading = false;
    }

    public function render()
    {
        return view(self::VIEW, [
            'title' => 'Client Details - #' . $this->transactionId,
            'client' => $this->client,
        ])->layout('themes::admin.default.views.layout');
    }
}
