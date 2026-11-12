@extends('layouts.app')

@section('content')

<style>
    .orders-page { padding: 8px 0 40px; }
    .orders-page h1 { font-size: 2rem; font-weight: 800; margin-bottom: 20px; color: #2b2b2b; }

    .orders-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,.06); overflow: hidden; }
    .orders-table { width: 100%; border-collapse: collapse; }
    .orders-table th { text-align: left; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em;
                       color: #6b7280; background: #f9fafb; padding: 14px 20px; }
    .orders-table td { padding: 16px 20px; border-top: 1px solid #f0f0f0; font-size: .9rem; color: #374151; vertical-align: middle; white-space: nowrap; }
    .orders-table tr:hover td { background: #fcf7f8; }

    .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: .75rem; font-weight: 700; }
    .badge-pending    { background: #fef3c7; color: #92400e; }
    .badge-processing { background: #dbeafe; color: #1e40af; }
    .badge-shipped    { background: #e0e7ff; color: #3730a3; }
    .badge-delivered  { background: #dcfce7; color: #166534; }
    .badge-completed  { background: #dcfce7; color: #166534; }
    .badge-cancelled  { background: #fee2e2; color: #991b1b; }

    .status-form { display: flex; align-items: center; gap: 8px; }
    .status-form select { padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: .85rem; background: #fff; }
    .btn-update { background: #9b1c3f; color: #fff; border: none; padding: 8px 16px; border-radius: 8px;
                  font-size: .8rem; font-weight: 700; cursor: pointer; transition: background .2s; }
    .btn-update:hover { background: #7d1633; }
    .btn-view { display: inline-flex; padding: 6px; border-radius: 8px; color: #6b7280; }
    .btn-view:hover { background: #f3f4f6; color: #9b1c3f; }
    .btn-view svg { width: 20px; height: 20px; }

    .flash { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: .9rem; }
    .flash-success { background: #dcfce7; color: #166534; }
    .flash-error   { background: #fee2e2; color: #991b1b; }
    .empty-row { text-align: center; padding: 40px !important; color: #9ca3af; }
</style>

<div class="orders-page">
    <h1>Order Management Dashboard</h1>

    @if (session('success')) <div class="flash flash-success">{{ session('success') }}</div> @endif
    @if (session('error'))   <div class="flash flash-error">{{ session('error') }}</div> @endif

    <div class="orders-card">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    @php $status = ucfirst(strtolower($order->status)); @endphp
                    <tr>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->user->name ?? 'Guest User' }}</td>
                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td><strong>${{ number_format($order->total_price, 2) }}</strong></td>
                        <td><span class="badge badge-{{ strtolower($status) }}">{{ $status }}</span></td>
                        <td>
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="status-form">
                                @csrf
                                @method('PUT')
                                <select name="status" required>
                                    @foreach (['Pending', 'Completed', 'Cancelled'] as $option)
                                        <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn-update">Update</button>
                                <a href="#" class="btn-view" title="View Details">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-row">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $orders->links('pagination::bootstrap-5') }} 
</div>

@endsection