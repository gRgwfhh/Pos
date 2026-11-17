<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $todaySales = Sale::whereDate('sale_date', today())->sum('total_price');
        $todayTransactions = Sale::whereDate('sale_date', today())
            ->distinct('receipt_number')
            ->count('receipt_number');

        return view('dashboard', [
            'todaySales' => $todaySales,
            'todayTransactions' => $todayTransactions,
            'lowStock' => Product::where('price', '>', 0)
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->get(),
            'availableStock' => Product::with('sackSize')
                ->where('price', '>', 0)
                ->where('stock', '>', 5)
                ->orderBy('type')
                ->orderBy('name')
                ->get(),
            'recentSales' => auth()->user()->hasRole('admin')
                ? Sale::with(['user', 'product'])->latest('sale_date')->limit(8)->get()
                : collect(),
        ]);
    }
}
