@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-head"><div><h1>Dashboard</h1><div class="muted">Store overview for {{ today()->format('F j, Y') }}</div></div><a class="button" href="{{ route('pos.index') }}">Start a sale</a></div>
    <div class="stats">
        <div class="stat"><small>Sales today</small><strong>₱{{ number_format((float) $todaySales, 2) }}</strong></div>
        <div class="stat"><small>Transactions today</small><strong>{{ $todayTransactions }}</strong></div>
        <div class="stat"><small>Products in catalog</small><strong>{{ \App\Models\Product::count() }}</strong></div>
    </div>
    <section class="panel" style="margin-bottom:20px">
        <div class="page-head"><h2 style="margin:0;font-size:19px">Available stock (more than 5)</h2><a href="{{ route('products.index') }}">View inventory</a></div>
        <div class="table-wrap"><table><thead><tr><th>Product</th><th>Category</th><th>Unit</th><th>Sack size</th><th>Price</th><th>Available</th></tr></thead><tbody>
            @forelse($availableStock as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ ucfirst($product->type) }}</td>
                    <td>{{ ucfirst($product->unit) }}</td>
                    <td>{{ $product->unit === 'kilo' && $product->sackSize ? rtrim(rtrim(number_format((float) $product->sackSize->weight_kg, 2, '.', ''), '0'), '.').' kg' : '—' }}</td>
                    <td>₱{{ number_format((float) $product->price, 2) }}</td>
                    <td>{{ number_format((float) $product->stock, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No products currently have more than 5 units in stock.</td></tr>
            @endforelse
        </tbody></table></div>
    </section>
    <section class="panel" style="margin-bottom:20px">
        <div class="page-head"><h2 style="margin:0;font-size:19px">Low stock (5 or fewer)</h2><a href="{{ route('products.index') }}">Manage inventory</a></div>
        <div class="table-wrap"><table><thead><tr><th>Product</th><th>Category</th><th>Unit</th><th>Available</th></tr></thead><tbody>
            @forelse($lowStock as $product)
                <tr><td>{{ $product->name }}</td><td>{{ ucfirst($product->type) }}</td><td>{{ ucfirst($product->unit) }}</td><td class="low">{{ number_format((float) $product->stock, 2) }}</td></tr>
            @empty<tr><td colspan="4">No products need restocking.</td></tr>@endforelse
        </tbody></table></div>
    </section>
    @if(auth()->user()->hasRole('admin'))
        <section class="panel">
            <div class="page-head"><h2 style="margin:0;font-size:19px">Recent sales</h2><a href="{{ route('sales.index') }}">View all sales</a></div>
            <div class="table-wrap"><table><thead><tr><th>Date</th><th>Receipt</th><th>Cashier</th><th>Product</th><th>Qty</th><th>Total</th></tr></thead><tbody>
                @forelse($recentSales as $sale)
                    <tr><td>{{ $sale->sale_date->format('M j, H:i') }}</td><td>{{ substr($sale->receipt_number, 0, 8) }}</td><td>{{ $sale->user->username }}</td><td>{{ $sale->product->name }}</td><td>{{ number_format((float) $sale->quantity, 2) }} {{ $sale->product->unit }}</td><td>₱{{ number_format((float) $sale->total_price, 2) }}</td></tr>
                @empty<tr><td colspan="6">No sales recorded yet.</td></tr>@endforelse
            </tbody></table></div>
        </section>
    @endif
@endsection
