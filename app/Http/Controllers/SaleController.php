<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        return view('sales.index', [
            'sales' => Sale::with(['user', 'product'])->latest('sale_date')->paginate(30),
        ]);
    }
}
