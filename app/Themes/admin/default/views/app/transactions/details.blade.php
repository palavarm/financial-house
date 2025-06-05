<x-slot:title>
    {{ $title }}
</x-slot>

<div class="card mb-4" style="padding: 0">
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
                            <th scope="col">Field</th>
                            <th scope="col">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Customer Number</td>
                            <td>{{ $transaction['customerInfo']['number'] }} - <a href="/client/{{ $transactionId }}">See Client >></a></td>
                        </tr>
                        <tr>
                            <td>Customer Name</td>
                            <td>{{ $transaction['customerInfo']['billingFirstName'] . ' ' . $transaction['customerInfo']['billingLastName'] }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $transaction['customerInfo']['email'] }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>{{ $transaction['transaction']['merchant']['status'] }}</td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td>{{ $transaction['fx']['merchant']['originalAmount'] }}</td>
                        </tr>
                        <tr>
                            <td>Currency</td>
                            <td>{{ $transaction['fx']['merchant']['originalCurrency'] }}</td>
                        </tr>
                        <tr>
                            <td>Merchant Name</td>
                            <td>{{ $transaction['merchant']['name'] }}</td>
                        </tr>
                        <tr>
                            <td>Created At</td>
                            <td>{{ $transaction['transaction']['merchant']['created_at'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@script
<script>
    let debounceTimer;

    function triggerReportUpdate(field, value) {
        clearTimeout(debounceTimer);

        const alpineRoot = document.querySelector('[x-data]');
        if (alpineRoot) {
            Alpine.$data(alpineRoot).loading = true;
        }

        debounceTimer = setTimeout(() => {
            $wire.set(field, value).then(() => {
                $wire.call('loadTransactions');
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
    }

    document.addEventListener('livewire:navigated', function () {
        initDatePickers();

        Livewire.hook('message.processed', (message, component) => {
            initDatePickers();
        });
    });
</script>
@endscript
