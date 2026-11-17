@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
    <div class="page-head"><div><h1>Inventory</h1><div class="muted">Update prices and on-hand quantity for each selling unit.</div></div><div class="actions">@if(auth()->user()->hasRole('admin'))<a class="button secondary" href="{{ route('sack-sizes.index') }}">Manage sack sizes</a>@endif<a class="button" href="{{ route('products.create') }}">Add product</a></div></div>
    <section class="panel table-wrap">
        <table><thead><tr><th>Product</th><th>Category</th><th>Unit</th><th>Sack size</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead><tbody>
        @forelse($products as $product)
            <tr>
                <td>{{ $product->name }}</td><td>{{ ucfirst($product->type) }}</td><td>{{ ucfirst($product->unit) }}</td>
                <td>{{ $product->unit === 'kilo' ? ($product->sackSize ? rtrim(rtrim(number_format((float) $product->sackSize->weight_kg, 2, '.', ''), '0'), '.').' kg' : 'Not assigned') : '—' }}</td>
                <td>₱{{ number_format((float) $product->price, 2) }}</td><td>{{ number_format((float) $product->stock, 2) }}</td>
                <td><div class="actions">
                    <a class="button secondary" href="{{ route('products.edit', $product) }}">Edit</a>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">@csrf @method('DELETE')<button class="danger" type="submit">Delete</button></form>
                </div></td>
            </tr>
        @empty<tr><td colspan="7">No products yet.</td></tr>@endforelse
        </tbody></table>
    </section>
@endsection
