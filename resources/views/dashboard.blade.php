<x-app-layout>
    <x-slot name="header">
        <bold>DASHBOARD</bold>
    </x-slot>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="storage/css/dashboard.css">

    <div class="dashboard-wrapper py-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-6">Overview</h2>

                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="summary-card-inner">
                            <div class="icon-box">
                                <img src="storage/img/totalitem.png" alt="error" class="menu-icon2">
                            </div>
                            <div class="summary-meta">
                                <div class="summary-value">{{ $itemCount }}</div>
                                <div class="summary-title">Total Item Counts</div>
                            </div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-inner">
                            <div class="icon-box">
                                <img src="storage/img/available-item.png" alt="error" class="available-icon">
                            </div>
                            <div class="summary-meta">

                                <div class="summary-value">{{ $itemRemaining }}</div>
                                <div class="summary-title">Available Items</div>
                            </div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-inner">
                            <div class="icon-box">
                                <img src="storage/img/packaging.png" alt="error" class="damaged-icon">
                            </div>
                            <div class="summary-meta">
                                <div class="damaged-value">{{ $damagedItem }}</div>
                                <div class="summary-title">Total Damaged Items</div>
                            </div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-inner">
                            <div class="icon-box">
                                <img src="storage/img/outbound.png" alt="error" class="menu-icon1">
                            </div>
                            <div class="summary-meta">
                                <div class="summary-value">{{ $outboundCount }}</div>
                                <div class="summary-title">Outbound Items</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="charts-section">

            <div class="charts-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">

                <div class="p-3 border rounded shadow-sm bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold text-gray-700 mb-0">Recent Issued Item Activity</h5>
                    </div>

                    <table class="table table-striped">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Personnel</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivities as $activity)
                                @php
                                    $remarkColor = [
                                        'To be delivered' =>
                                            'bg-info text-dark                                                                                                                      ',
                                        'Not Receive' => 'bg-danger',
                                        'Issued' => 'bg-warning text-dark ',
                                        'Received' => 'bg-success',
                                        'Returned' => 'bg-primary ',
                                    ];
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        {{ $activity->personnel->personnel_name ?? 'Unknown Personnel' }}</td>
                                    <td class="text-center">{{ $activity->item->item_name ?? 'Unknown Item' }}</td>
                                    <td class="text-center">{{ $activity->personnel_item_quantity }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $remarkColor[$activity->personnel_item_remarks] ?? 'bg-secondary' }}">
                                            {{ $activity->personnel_item_remarks ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ $activity->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
                                        <br>
                                        <small>{{ $activity->created_at->setTimezone('Asia/Manila')->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No recent activity</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="top-zones-section p-3 border rounded shadow-sm bg-white">
                    <h5 class="mb-3 fw-bold text-gray-700">Calendar</h5>
                    <div class="calendar-wrapper">
                        <div id="calendar"></div>
                    </div>
                </div>

            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        

        <script>
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: @json(session('success')),
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const monthFilter = document.getElementById('monthFilter');
                monthFilter.addEventListener('change', function() {
                    const selectedMonth = this.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('month', selectedMonth);
                    window.location.href = url.toString();
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth'
                });

                calendar.render();
            });
        </script>

</x-app-layout>
