<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Personnel & Outbound Management
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-5">
                <h5 class="text-secondary fw-bold mb-3">Item Type Overview</h5>
                <div class="d-flex flex-nowrap overflow-auto pb-3" style="gap: 1.2rem; scrollbar-width: thin;">

                    @foreach ($categoriesWithStats as $index => $stat)
                        @if ($stat['total'] > 0)
                            <div class="flex-shrink-0" style="width: 280px;">
                                <div class="card border-0 shadow-sm text-center p-3 h-100 bg-white dark:bg-gray-800">
                                    <h6 class="fw-bold text-gray-700 dark:text-gray-300 text-truncate mb-3"
                                        title="{{ $stat['name'] }}">
                                        @if ($stat['icon'])
                                            <i class="bi {{ $stat['icon'] }} me-2 text-primary"></i>
                                        @endif
                                        {{ $stat['name'] }}
                                    </h6>

                                    <div class="position-relative mx-auto mb-3" style="height: 140px; width: 140px;">
                                        <canvas id="chart-{{ $index }}"></canvas>
                                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                                            <small class="text-muted d-block"
                                                style="font-size: 0.65rem; line-height: 1;">Total</small>
                                            <span
                                                class="fw-bold fs-5 text-gray-800 dark:text-white">{{ $stat['total'] }}</span>
                                        </div>
                                    </div>

                                    <div class="row g-0 border-top dark:border-gray-700 pt-2 mt-2 text-center">
                                        <div class="col-4 border-end dark:border-gray-700" title="All Good Items">
                                            <p class="text-muted mb-0" style="font-size: 0.65rem;">Good</p>
                                            <small class="fw-bold text-success">{{ $stat['available'] }}</small>
                                        </div>
                                        <div class="col-4 border-end dark:border-gray-700" title="Outbound Items">
                                            <p class="text-muted mb-0" style="font-size: 0.65rem;">Taken</p>
                                            <small class="fw-bold text-primary">{{ $stat['taken'] }}</small>
                                        </div>
                                        <div class="col-4" title="Damaged">
                                            <p class="text-muted mb-0" style="font-size: 0.65rem;">Damaged</p>
                                            <small class="fw-bold text-danger">{{ $stat['broken'] }}</small>
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
                                                labels: ['Good (Available)', 'Taken (Outbound)', 'Damaged', 'Deprecated'],
                                                datasets: [{
                                                    data: [
                                                        {{ $stat['available'] }},
                                                        {{ $stat['taken'] }},
                                                        {{ $stat['broken'] }},
                                                        {{ $stat['deprecated'] ?? 0 }}
                                                    ],
                                                    backgroundColor: [
                                                        '#198754', // Green - Good
                                                        '#0d6efd', // Blue - Taken/Outbound
                                                        '#dc3545', // Red - Broken/Damaged
                                                        '#6c757d' // Gray - Deprecated
                                                    ],
                                                    borderWidth: 0,
                                                    cutout: '75%'
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                animation: {
                                                    duration: 1000
                                                },
                                                plugins: {
                                                    legend: {
                                                        display: false
                                                    },
                                                    tooltip: {
                                                        enabled: true,
                                                        callbacks: {
                                                            label: function(context) {
                                                                return ' ' + context.label + ': ' + context.raw;
                                                            }
                                                        }
                                                    }
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
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2"></i>Recent Activities</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Personnel</th>
                                    <th>Item Details</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                    <th>Reasons / Remarks</th>
                                    <th>Condition</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                    <tr>
                                        {{-- Personnel Name --}}
                                        <td>
                                            {{ $activity->personnel->personnel_name ?? 'N/A' }}
                                        </td>

                                        {{-- Item Info --}}
                                        <td>
                                            <span class="fw-bold text-dark d-block">
                                                {{ $activity->item->item_name ?? 'N/A' }}
                                            </span>
                                            <small class="text-muted">
                                                Item Type:
                                                {{ $activity->item->category->item_category_name ?? '-' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                Serial No:
                                                {{ $activity->item->item_serialno ?? '-' }}
                                            </small>
                                        </td>

                                        {{-- Quantity --}}
                                        <td>
                                            {{ $activity->personnel_item_quantity }} pcs
                                        </td>

                                        {{-- Date Updated --}}
                                        <td>
                                            {{ $activity->updated_at->format('M d, Y h:i A') }}
                                        </td>

                                        {{-- Return Reason --}}
                                        <td class="px-6 py-4">
                                            @if ($activity->return_reason_preset)
                                                <span class="d-block fw-semibold text-dark">
                                                    {{ ucwords(str_replace('_', ' ', $activity->return_reason_preset)) }}
                                                </span>

                                                @if ($activity->return_reason_detail)
                                                    <small class="text-muted d-block mt-1">
                                                        "{{ $activity->return_reason_detail }}"
                                                    </small>
                                                @endif
                                            @else
                                                <small class="text-muted fst-italic">
                                                    {{ $activity->personnel_item_remarks ?? 'Not specified' }}
                                                </small>
                                            @endif
                                        </td>

                                        {{-- Item Condition --}}
                                        <td>
                                            @php
                                                $remark = $activity->item->item_remark ?? 'Good';
                                            @endphp

                                            @if ($remark == 'Good')
                                                <span class="badge bg-success">Good</span>
                                            @elseif($remark == 'Damaged')
                                                <span class="badge bg-danger">Damaged</span>
                                            @elseif($remark == 'Deprecated')
                                                <span class="badge bg-warning text-dark">Deprecated</span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    {{ $remark }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Activity Type --}}
                                        <td>
                                            @if (in_array($activity->personnel_item_remarks, ['Received', 'Not Receive']))
                                                <span class="badge bg-primary">
                                                    Outbound
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    Returned
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No recent activities found.
                                        </td>
                                    </tr>

                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (method_exists($recentActivities, 'hasPages') && $recentActivities->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        {{ $recentActivities->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
