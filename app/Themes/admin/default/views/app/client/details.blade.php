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
                            <td>{{ $client['number'] }}</td>
                        </tr>
                        <tr>
                            <td>Customer Name</td>
                            <td>{{ $client['billingFirstName'] . ' ' . $client['billingLastName'] }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $client['email'] }}</td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>{{ $client['gender'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Birthday</td>
                            <td>{{ $client['birthday'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Billing Address</td>
                            <td>
                                {{ $client['billingAddress1'] . ' ' . $client['billingAddress2'] ?? '' }} <br>
                                {{ $client['billingCity'] . ' ' . $client['billingPostcode'] . ' ' . $client['billingCountry'] }}
                            </td>
                        </tr>
                        <tr>
                            <td>Shipping Address</td>
                            <td>
                                {{ $client['shippingAddress1'] . ' ' . $client['shippingAddress2'] ?? '' }} <br>
                                {{ $client['shippingCity'] . ' ' . $client['shippingPostcode'] . ' ' . $client['shippingCountry'] }}
                            </td>
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
