@extends('layouts.app')

@section('content')
<a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
    <i class="bi bi-arrow-left"></i> Back to orders
</a>


<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <p class="mb-1"><strong>Customer:</strong> {{ $order->user->name ?? 'Unknown' }}</p>
        <p class="mb-1"><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
        <p class="mb-0"><strong>Status:</strong> {{ $order->status }}</p>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th>Product</th><th>Qty</th><th>Price</th></tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Deleted product' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{ $orders->links('pagination::bootstrap-5') }}
<p class="text-end fs-5 fw-bold mt-3">Total: ${{ number_format($order->total_price, 2) }}</p>
@endsection