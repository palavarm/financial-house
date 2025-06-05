<?php

namespace App\Packages\RpdPayment\Views\Admin;

use App\Packages\RpdPayment\Facades\ReportingApiService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class TransactionsList extends Component
{
    const VIEW = 'themes::admin.default.views.app.transactions.list';
    const STATUSES = [
        "APPROVED",
        "WAITING",
        "DECLINED",
        "ERROR",
    ];
    const PAYMENT_METHODS = [
        "CREDITCARD",
        "CUP",
        "IDEAL",
        "GIROPAY",
        "MISTERCASH",
        "STORED",
        "PAYTOCARD",
        "CEPBANK",
        "CITADEL"
    ];

    public string $fromDate;
    public string $toDate;
    public ?string $status = null;
    public ?string $paymentMethod = null;
    public array $transactions = [];
    public bool $isLoading = false;
    private ?Carbon $lastUpdated = null;
    private int $currentPage = 1;
    private ?int $prevPage = null;
    private ?int $nextPage = null;

    public function mount(): void
    {
        $this->loadTransactions();
    }

    public function loadTransactions(?array $params = null): void
    {
        $now = now();
        if ($this->lastUpdated && $now->diffInMilliseconds($this->lastUpdated) < 100) {
            return; // debounce threshold
        }

        $this->lastUpdated = $now;
        $this->isLoading = true;

        //dd($params);

        $this->fromDate = $params['fromDate'] ?? (request()->get('fromDate') ?? now()->subMonths(6)->toDateString());
        $this->toDate = $params['toDate'] ?? (request()->get('toDate') ?? now()->toDateString());
        $this->status = $params['status'] ?? request()->get('status', null);
        $this->paymentMethod = $params['paymentMethod'] ?? request()->get('paymentMethod', null);
        $page = $params['p'] ?? request()->get('p', null);

        $params = [
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'status' => $this->status,
            'paymentMethod' => $this->paymentMethod,
        ];

        if (in_array($this->status, self::STATUSES)) {
            $params['status'] = $this->status;
        }

        if (in_array($this->paymentMethod, self::PAYMENT_METHODS)) {
            $params['paymentMethod'] = $this->paymentMethod;
        }

        $response = ReportingApiService::transactionList($params, $page);

        $this->transactions = $response['data'] ?? [];

        $this->currentPage = $response['current_page'];
        $nextPage = $response['next_page_url'];
        $prevPage = $response['prev_page_url'];

        if ($nextPage) {
            parse_str(parse_url($nextPage, PHP_URL_QUERY), $queryParams);
            $this->nextPage = $queryParams['page'] ?? 1;
        }

        if ($prevPage) {
            parse_str(parse_url($prevPage, PHP_URL_QUERY), $queryParams);
            $this->prevPage = $queryParams['page'] ?? 1;
        }

        $this->isLoading = false;
    }

    public function render()
    {
        return view(self::VIEW, [
            'title' => 'Transaction List',
            'transactions' => $this->transactions,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'status' => $this->status,
            'paymentMethod' => $this->paymentMethod,
            'currentPage' => $this->currentPage,
            'nextPage' => $this->nextPage,
            'prevPage' => $this->prevPage,
        ])->layout('themes::admin.default.views.layout');
    }
}
