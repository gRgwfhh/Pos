@extends('layouts.app')

@section('title', $product->exists ? 'Edit product' : 'Add product')

@section('content')
    <div class="page-head"><div><h1>{{ $product->exists ? 'Edit product' : 'Add product' }}</h1><div class="muted">Kilo products can be linked to the size of the sack they come from.</div></div><a class="button secondary" href="{{ route('products.index') }}">Back to inventory</a></div>
    <section class="panel">
        <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
            @csrf
            @if($product->exists) @method('PUT') @endif
            <div class="form-grid">
                <div class="field"><label for="name">Product name</label><input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="255" placeholder="e.g. Premium rice"></div>
                <div class="field"><label for="type">Category</label><select id="type" name="type" required>@foreach($types as $type)<option value="{{ $type }}" @selected(old('type', $product->type) === $type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
                <div class="field"><label for="unit">Selling unit</label><select id="unit" name="unit" required><option value="kilo" @selected(old('unit', $product->unit) === 'kilo')>Kilo</option><option value="sack" @selected(old('unit', $product->unit) === 'sack')>Sack</option></select></div>
                <div class="field" id="sack-size-field">
                    <label for="sack_size_id">Sack size this kilo comes from</label>
                    <select id="sack_size_id" name="sack_size_id">
                        <option value="">Select a sack size</option>
                        @foreach($sackSizes as $sackSize)
                            <option value="{{ $sackSize->id }}" @selected((string) old('sack_size_id', $product->sack_size_id) === (string) $sackSize->id)>{{ rtrim(rtrim(number_format((float) $sackSize->weight_kg, 2, '.', ''), '0'), '.') }} kg sack</option>
                        @endforeach
                    </select>
                    @if(auth()->user()->hasRole('admin'))
                        <a class="muted" href="{{ route('sack-sizes.index') }}">Manage sack-size options</a>
                    @endif
                </div>
                <div class="field"><label for="price">Price per unit (₱)</label><input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? '0.00') }}" required></div>
                <div class="field"><label for="stock">Current stock (units)</label><input id="stock" type="number" step="0.01" min="0" name="stock" value="{{ old('stock', $product->stock ?? '0.00') }}" required></div>
            </div>
            <button type="submit">Save product</button>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        const unitSelect = document.getElementById('unit');
        const sackSizeField = document.getElementById('sack-size-field');
        const sackSizeSelect = document.getElementById('sack_size_id');

        function updateSackSizeVisibility() {
            const sellsByKilo = unitSelect.value === 'kilo';
            sackSizeField.hidden = !sellsByKilo;
            sackSizeSelect.required = sellsByKilo;

            if (!sellsByKilo) {
                sackSizeSelect.value = '';
            }
        }

        unitSelect.addEventListener('change', updateSackSizeVisibility);
        updateSackSizeVisibility();
    </script>
@endpush
