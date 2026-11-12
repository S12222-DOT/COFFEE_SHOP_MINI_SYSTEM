<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // cancelled orders are not real sales (status may be stored in lowercase)
        $sales = fn () => Order::whereRaw('LOWER(status) != ?', ['cancelled']);

        $stats = [
            'users'    => User::where('role', '!=', 1)->count(),   // customers only
            'revenue'  => $sales()->sum('total_price'),
            'products' => Product::count(),
            'orders'   => Order::count(),
        ];

        // top 5 products by quantity sold
        $topProducts = OrderItem::selectRaw('product_id, SUM(quantity) as sold')
            ->whereHas('order', fn ($q) => $q->whereRaw('LOWER(status) != ?', ['cancelled']))
            ->groupBy('product_id')
            ->orderByDesc('sold')
            ->with('product:id,name')
            ->take(5)
            ->get();

        // revenue for the last 6 months, months with no sales count as 0
        $byMonth = $sales()
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(total_price) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $values = [];
        for ($i = 5; $i >= 0; $i--) {
            $m        = now()->subMonths($i);
            $labels[] = $m->format('M Y');
            $values[] = (float) ($byMonth[$m->format('Y-m')] ?? 0);
        }

        return view('dashboard.admin', compact('stats', 'topProducts', 'labels', 'values'));
    }
}