<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Personnel & Outbound Management
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-5">
                <h5 class="text-secondary fw-bold mb-3">Item Usage Overview</h5>
                <div class="d-flex flex-nowrap overflow-auto pb-3" style="gap: 1.2rem; scrollbar-width: thin;">
                    @foreach ($itemsWithStats as $index => $stat)
                        @if ($stat['available'] > 0 || $stat['percentage_taken'] > 0)
                            <div class="flex-shrink-0" style="width: 260px;">
                                <div class="card border-0 shadow-sm text-center p-3 h-100 bg-white dark:bg-gray-800">
                                    <h6 class="fw-bold text-gray-700 dark:text-gray-300 text-truncate mb-3" title="{{ $stat['name'] }}">
                                        {{ $stat['name'] }}
                                    </h6>

                                    <div class="position-relative mx-auto mb-3" style="height: 130px; width: 130px;">
                                        <canvas id="chart-{{ $index }}"></canvas>
                                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                                            <small class="text-muted d-block" style="font-size: 0.65rem; line-height: 1;">Taken</small>
                                            <span class="fw-bold fs-5 text-gray-800 dark:text-white">{{ $stat['percentage_taken'] }}%</span>
                                        </div>
                                    </div>

                                    <div class="row g-0 border-top dark:border-gray-700 pt-2 mt-2">
                                        <div class="col-4 border-end dark:border-gray-700">
                                            <p class="text-muted mb-0" style="font-size: 0.65rem;">Avail</p>
                                            <small class="fw-bold text-success">{{ $stat['available'] }}</small>
                                        </div>
                                        <div class="col-4 border-end dark:border-gray-700">
                                            <p class="text-muted mb-0" style="font-size: 0.65rem;">Broken</p>
                                            <small class="fw-bold text-danger">{{ $stat['broken'] }}</small>
                                        </div>
                                        <div class="col-4">
                                            <p class="text-muted mb-0" style="font-size: 0.65rem;">Depr</p>
                                            <small class="fw-bold text-secondary">{{ $stat['deprecated'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                (function() {
                                    const initChart = () => {
                                        const canvas = document.getElementById('chart-{{ $index }}');
                                        if (!canvas) return;

                                        new Chart(canvas, {
                                            type: 'doughnut',
                                            data: {
                                                datasets: [{
                                                    // data[0] is Taken, data[1] is Remaining
                                                    data: [{{ $stat['percentage_taken'] }}, {{ 100 - $stat['percentage_taken'] }}],
                                                    backgroundColor: ['#17a2b8', '#f3f4f6'],
                                                    borderWidth: 0,
                                                    cutout: '82%'
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                animation: { duration: 1000 },
                                                plugins: {
                                                    legend: { display: false },
                                                    tooltip: { enabled: false }
                                                }
                                            }
                                        });
                                    };

                                    if (document.readyState === 'complete') {
                                        initChart();
                                    } else {
                                        window.addEventListener('load', initChart);
                                    }
                                })();
                            </script>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-warning py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2"></i>Return History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Personnel</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Date Returned</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outbounds as $outbound)
                                    <tr>
                                        <td>{{ $outbound->personnel->personnel_name }}</td>
                                        <td>{{ $outbound->item->item_name }}</td>
                                        <td>{{ $outbound->personnel_item_quantity }} pcs</td>
                                        <td>{{ $outbound->updated_at->format('M d, Y h:i A') }}</td>
                                        <td><span class="badge bg-success">Returned</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No return records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($outbounds->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $outbounds->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>