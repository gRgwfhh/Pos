@extends('layouts.app')

@section('title', 'Sales')

@section('content')
    <div class="page-head"><div><h1>Sales history</h1><div class="muted">Each row is one item on a receipt.</div></div></div>
    <section class="panel table-wrap">
        <table><thead><tr><th>Date</th><th>Receipt</th><th>Cashier</th><th>Product</th><th>Qty</th><th>Unit price</th><th>Total</th></tr></thead><tbody>
        @forelse($sales as $sale)
            <tr><td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td><td>{{ substr($sale->receipt_number, 0, 8) }}</td><td>{{ $sale->user->username }}</td><td>{{ $sale->product->name }}</td><td>{{ number_format((float) $sale->quantity, 2) }} {{ $sale->product->unit }}</td><td>₱{{ number_format((float) $sale->unit_price, 2) }}</td><td>₱{{ number_format((float) $sale->total_price, 2) }}</td></tr>
        @empty<tr><td colspan="7">No sales recorded yet.</td></tr>@endforelse
        </tbody></table>
        <div class="pagination">{{ $sales->links() }}</div>
    </section>
@endsection
