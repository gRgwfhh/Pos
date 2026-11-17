@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')
    <div class="page-head"><div><h1>Point of Sale</h1><div class="muted">Select products, enter quantities, and complete the sale.</div></div></div>
    <div class="split">
        <section><div class="grid">
            @forelse($products as $product)
                <article class="product-card">
                    <h3>{{ $product->name }}</h3>
                    <div class="muted">{{ ucfirst($product->type) }} · {{ ucfirst($product->unit) }} · {{ number_format((float) $product->stock, 2) }} available</div>
                    @if($product->unit === 'kilo' && $product->sackSize)
                        <div class="muted">From {{ rtrim(rtrim(number_format((float) $product->sackSize->weight_kg, 2, '.', ''), '0'), '.') }} kg sack</div>
                    @endif
                    <div class="price">₱{{ number_format((float) $product->price, 2) }} / {{ $product->unit }}</div>
                    <button type="button" class="add-to-cart" data-id="{{ $product->id }}" data-name="{{ $product->name }}{{ $product->unit === 'kilo' && $product->sackSize ? ' - '.$product->sackSize->weight_kg.' kg sack' : '' }}" data-unit="{{ $product->unit }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">Add to sale</button>
                </article>
            @empty
                <div class="panel">No stock is currently available. Update inventory before selling.</div>
            @endforelse
        </div></section>
        <aside class="panel">
            <h2 style="margin-top:0">Current sale</h2>
            <form method="POST" action="{{ route('pos.checkout') }}" id="checkout-form">
                @csrf
                <div id="cart-lines"><p class="muted">No items added.</p></div>
                <div class="total"><span>Total</span><span id="cart-total">₱0.00</span></div>
                <button type="submit" style="width:100%">Complete sale</button>
            </form>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
(() => {
    const cart = new Map();
    const lines = document.getElementById('cart-lines');
    const total = document.getElementById('cart-total');
    const money = value => `₱${value.toFixed(2)}`;

    function render() {
        lines.replaceChildren();
        let sum = 0;
        if (cart.size === 0) {
            const empty = document.createElement('p');
            empty.className = 'muted';
            empty.textContent = 'No items added.';
            lines.append(empty);
        }
        for (const [id, item] of cart) {
            const lineTotal = item.price * item.quantity;
            sum += lineTotal;
            const row = document.createElement('div');
            row.className = 'cart-line';
            const label = document.createElement('span');
            label.textContent = `${item.name} (${money(lineTotal)})`;
            const quantity = document.createElement('input');
            quantity.type = 'number';
            quantity.min = '0.01';
            quantity.max = item.stock;
            quantity.step = '0.01';
            quantity.value = item.quantity.toFixed(2);
            quantity.setAttribute('aria-label', `Quantity of ${item.name}`);
            quantity.addEventListener('change', () => {
                const next = Number(quantity.value);
                if (!Number.isFinite(next) || next <= 0) cart.delete(id);
                else item.quantity = Math.min(next, item.stock);
                render();
            });
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'danger';
            remove.textContent = 'Remove';
            remove.addEventListener('click', () => { cart.delete(id); render(); });
            const hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = `items[${id}][product_id]`;
            hiddenId.value = id;
            const hiddenQuantity = document.createElement('input');
            hiddenQuantity.type = 'hidden';
            hiddenQuantity.name = `items[${id}][quantity]`;
            hiddenQuantity.value = item.quantity.toFixed(2);
            row.append(label, quantity, remove, hiddenId, hiddenQuantity);
            lines.append(row);
        }
        total.textContent = money(sum);
    }

    document.querySelectorAll('.add-to-cart').forEach(button => button.addEventListener('click', () => {
        const id = button.dataset.id;
        const current = cart.get(id);
        const stock = Number(button.dataset.stock);
        if (current) current.quantity = Math.min(current.quantity + 1, stock);
        else cart.set(id, {
            name: button.dataset.name,
            unit: button.dataset.unit,
            price: Number(button.dataset.price),
            stock,
            quantity: Math.min(1, stock),
        });
        render();
    }));

    document.getElementById('checkout-form').addEventListener('submit', event => {
        if (cart.size === 0) {
            event.preventDefault();
            alert('Add at least one product to the sale.');
        }
    });
})();
</script>
@endpush
