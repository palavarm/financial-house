<x-slot:title>
    {{ $title }}
</x-slot>

<div class="card mb-4" style="padding: 0">
    <div class="card-header">
        <div class="container">
            <div class="row">
                <div class="col-3">
                    <input type="text" wire:model.change="fromDate" class="form-control datepicker" id="fromDate" placeholder="From Date" required>
                </div>
                <div class="col-3">
                    <input type="text" wire:model.change="toDate" class="form-control datepicker" id="toDate" placeholder="To Date" required>
                </div>
                <div class="col-3">
                    <select name="status" wire:model.change="status" id="status" class="form-select">
                        <option value="" selected hidden>Status</option>
                        <option value="APPROVED">APPROVED</option>
                        <option value="WAITING">WAITING</option>
                        <option value="DECLINED">DECLINED</option>
                        <option value="ERROR">ERROR</option>
                    </select>
                </div>
                <div class="col-3">
                    <select name="paymentMethod" wire:model.change="paymentMethod" id="paymentMethod" class="form-select">
                        <option value="" selected hidden>Payment Method</option>
                        <option value="CREDITCARD">CREDITCARD</option>
                        <option value="CUP">CUP</option>
                        <option value="IDEAL">IDEAL</option>
                        <option value="GIROPAY">GIROPAY</option>
                        <option value="MISTERCASH">MISTERCASH</option>
                        <option value="STORED">STORED</option>
                        <option value="PAYTOCARD">PAYTOCARD</option>
                        <option value="CEPBANK">CEPBANK</option>
                        <option value="CITADEL">CITADEL</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body position-relative">
        <div wire:loading wire:target="loadReports" class="overlay" style="text-align: center; padding-top: 30px;">
            <div class="spinner-border text-primary" role="status" style="display: inline-block;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div x-data="{ loading: @entangle('isLoading') }">
            <div class="table-wrapper" :class="{ 'opacity-50 pointer-events-none': loading }">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Fullname</th>
                            <th scope="col">Merchant</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Currency</th>
                            <th scope="col">Payment Method</th>
                            <th scope="col">Status</th>
                            <th scope="col">Created At</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <th scope="row">{{ $transaction['transaction']['merchant']['transactionId'] }}</th>
                            <td>{{ $transaction['customerInfo']['billingFirstName'] . ' ' . $transaction['customerInfo']['billingLastName'] }}</td>
                            <td>{{ $transaction['merchant']['name'] }}</td>
                            <td>{{ $transaction['fx']['merchant']['originalAmount'] }}</td>
                            <td>{{ $transaction['fx']['merchant']['originalCurrency'] }}</td>
                            <td>{{ $transaction['acquirer']['type'] ?? '' }}</td>
                            <td>{{ $transaction['transaction']['merchant']['status'] }}</td>
                            <td>{{ $transaction['created_at'] }}</td>
                            <td>
                                <a href="/transaction/{{ $transaction['transaction']['merchant']['transactionId'] }}">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <li class="page-item {{ (!$prevPage) ? 'disabled' : '' }}">
                    <a
                        class="page-link"
                        @if ($prevPage)
                            href="/transactions?fromDate={{$fromDate}}&toDate={{$toDate}}{{ $status ? '&status='.$status : '' }}{{ $paymentMethod ? '&paymentMethod='.$paymentMethod : '' }}&p={{$prevPage}}"
                        @endif
                        aria-label="Previous"
                    >
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link active">Current Page: {{ $currentPage }}</a>
                </li>
                <li class="page-item {{ (!$nextPage) ? 'disabled' : '' }}">
                    <a
                        class="page-link"
                        @if ($nextPage)
                            href="/transactions?fromDate={{$fromDate}}&toDate={{$toDate}}{{ $status ? '&status='.$status : '' }}{{ $paymentMethod ? '&paymentMethod='.$paymentMethod : '' }}&p={{$nextPage}}"
                        @endif
                        aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

@script
<script>
    let debounceTimer;

    function updateBrowserUrl(params) {
        const url = new URL(window.location.href);

        // Clear current search params
        url.search = '';

        // Rebuild search params
        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                url.searchParams.set(key, value);
            }
        });

        // Update the browser URL without reloading
        window.history.pushState({}, '', url);
    }


    function getCurrentPageParams() {
        const params = new URLSearchParams(window.location.search);
        const result = {};

        for (const [key, value] of params.entries()) {
            result[key] = value;
        }

        return result;
    }

    function triggerReportUpdate(field, value) {
        clearTimeout(debounceTimer);

        const alpineRoot = document.querySelector('[x-data]');
        if (alpineRoot) {
            Alpine.$data(alpineRoot).loading = true;
        }

        debounceTimer = setTimeout(() => {
            const params = getCurrentPageParams();
            params[field] = value; // update the specific filter in the param object

            updateBrowserUrl(params); // update URL dynamically

            $wire.set(field, value).then(() => {
                $wire.call('loadTransactions', params);
            });
        }, 300);
    }

    function initDatePickers() {
        $('#fromDate').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function(dateText) {
                triggerReportUpdate('fromDate', dateText);
            }
        });

        $('#toDate').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function(dateText) {
                triggerReportUpdate('toDate', dateText);
            }
        });

        $('#status').on('change', function () {
            triggerReportUpdate('status', $(this).val());
        });

        $('#paymentMethod').on('change', function () {
            triggerReportUpdate('paymentMethod', $(this).val());
        });
    }

    document.addEventListener('livewire:navigated', function () {
        initDatePickers();

        Livewire.hook('message.processed', (message, component) => {
            initDatePickers();
        });
    });
</script>
@endscript
