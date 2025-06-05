<x-slot:title>
    {{ $reportData['title'] }}
</x-slot>

<div class="card mb-4" style="padding: 0">
    <div class="card-header">
        <div class="container">
            <div class="row">
                <div class="col-3">
                    <input type="text" wire:model.change="fromDate" class="form-control datepicker" id="fromDate" placeholder="From Date">
                </div>
                <div class="col-3">
                    <input type="text" wire:model.change="toDate" class="form-control datepicker" id="toDate" placeholder="To Date">
                </div>
                <div class="col-3">
                    <input type="text" wire:model.change="merchantId" class="form-control" id="merchantId" placeholder="Merchant Id">
                </div>
                <div class="col-3">
                    <input type="text" wire:model.change="acquirer" class="form-control" id="acquirer" placeholder="Acquirer">
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
                        <th scope="col">#</th>
                        <th scope="col">Currency</th>
                        <th scope="col">Total Count</th>
                        <th scope="col">Total Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['reports'] as $report)
                        <tr>
                            <th scope="row">{{ ++$loop->index }}</th>
                            <td>{{ $report['currency'] }}</td>
                            <td>{{ $report['count'] }}</td>
                            <td>{{ $report['total'] }}</td>
                        </tr>
                        @endforeach
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
                $wire.call('loadReports');
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

        $('#merchantId').on('change', function () {
            triggerReportUpdate('merchantId', $(this).val());
        });

        $('#acquirer').on('change', function () {
            triggerReportUpdate('acquirer', $(this).val());
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
