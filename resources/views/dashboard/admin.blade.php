@extends('layouts.app')
{{-- Assumes 'layouts.app' is a master layout that defines the <head>, <body>, and .dashboard-wrapper --}}

@section('content')

{{-- Needs from DashboardController@index: $stats, $topProducts, $labels, $values --}}
<div class="main-content">
    <div class="container-fluid p-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="dashboard-title fs-2 mb-0">Dashboard Overview</h1>
        </div>

        {{-- 4 stat cards --}}
        <div class="row g-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card p-4 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="me-4 p-3 rounded-circle bg-maroon-light flex-shrink-0">
                            <i class="bi bi-people text-maroon fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 fw-bold text-uppercase small">Customers</p>
                            <h3 class="fw-bolder mb-0 text-dark-brown fs-3">{{ number_format($stats['users']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card p-4 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="me-4 p-3 rounded-circle bg-success-subtle flex-shrink-0">
                            <i class="bi bi-currency-dollar text-success fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 fw-bold text-uppercase small">Revenue</p>
                            <h3 class="fw-bolder mb-0 text-dark-brown fs-3">${{ number_format($stats['revenue'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card p-4 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="me-4 p-3 rounded-circle bg-info-subtle flex-shrink-0">
                            <i class="bi bi-bag text-info fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 fw-bold text-uppercase small">Products</p>
                            <h3 class="fw-bolder mb-0 text-dark-brown fs-3">{{ number_format($stats['products']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card p-4 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="me-4 p-3 rounded-circle bg-info-subtle flex-shrink-0">
                            <i class="bi bi-receipt-cutoff text-info fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-1 fw-bold text-uppercase small">Total Orders</p>
                            <h3 class="fw-bolder mb-0 text-dark-brown fs-3">{{ number_format($stats['orders']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- chart + top products --}}
        <div class="row g-4 mt-4">
            <div class="col-lg-8">
                <div class="card stat-card p-4 bg-white h-100">
                    <h4 class="fw-bold text-dark-brown">Sales Performance</h4>
                    <p class="text-muted">Revenue for the last 6 months (cancelled orders not counted).</p>
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card stat-card p-4 bg-white h-100">
                    <h4 class="fw-bold text-dark-brown">Top Products</h4>
                    <ul class="list-group list-group-flush mt-3">
                        @forelse ($topProducts as $row)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                {{ $row->product->name ?? 'Deleted product' }}
                                <span class="badge bg-maroon rounded-pill">{{ $row->sold }} sold</span>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-secondary">No sales yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Revenue ($)',
                data: @json($values),
                backgroundColor: '#843f3f',
                borderRadius: 8
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

@endsection

@include('layouts.modals') {{-- The logout modal should be included here --}}