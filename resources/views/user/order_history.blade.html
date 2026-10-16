@extends('layouts.user_app')

@section('content')

<div class="container mx-auto px-4 py-8">
<h1 class="text-4xl font-extrabold text-gray-900 mb-8 border-b pb-2">Your Order History</h1>

@if ($orders->isEmpty())
    <div class="text-center bg-white p-10 rounded-lg shadow-md">
        <p class="text-2xl text-gray-600 mb-4">You have not placed any orders yet.</p>
        <a href="{{ route('menu.index') }}" class="inline-block bg-indigo-500 text-white px-6 py-2 rounded-lg hover:bg-indigo-600 transition">
            Start Shopping
        </a>
    </div>
@else
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($orders as $order)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">${{ number_format($order->total_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $color = [
                                    'Pending' => 'yellow', 
                                    'Processing' => 'blue', 
                                    'Shipped' => 'indigo', 
                                    'Delivered' => 'green',
                                    'Cancelled' => 'red'
                                ][$order->status] ?? 'gray';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('order.details', $order) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@endif


</div>
@endsection