@extends('layouts.app')

@section('title', 'Sack sizes')

@section('content')
    <div class="page-head">
        <div>
            <h1>Sack sizes</h1>
            <div class="muted">Manage the options staff can assign to kilo products.</div>
        </div>
        <a class="button secondary" href="{{ route('products.index') }}">Back to inventory</a>
    </div>

    <section class="panel" style="margin-bottom:20px">
        <h2 style="margin-top:0;font-size:19px">Add sack size</h2>
        <form class="actions" method="POST" action="{{ route('sack-sizes.store') }}">
            @csrf
            <div class="field" style="margin:0;min-width:220px">
                <label for="weight_kg">Weight in kilograms</label>
                <input id="weight_kg" type="number" step="0.01" min="0.01" name="weight_kg" value="{{ old('weight_kg') }}" required>
            </div>
            <button type="submit">Add size</button>
        </form>
    </section>

    <section class="panel table-wrap">
        <table>
            <thead><tr><th>Sack size</th><th>Assigned products</th><th>Update</th><th>Delete</th></tr></thead>
            <tbody>
            @forelse($sackSizes as $sackSize)
                <tr>
                    <td>{{ rtrim(rtrim(number_format((float) $sackSize->weight_kg, 2, '.', ''), '0'), '.') }} kg</td>
                    <td>{{ $sackSize->products_count }}</td>
                    <td>
                        <form class="inline-form" method="POST" action="{{ route('sack-sizes.update', $sackSize) }}">
                            @csrf
                            @method('PUT')
                            <input aria-label="Weight in kilograms" type="number" step="0.01" min="0.01" name="weight_kg" value="{{ $sackSize->weight_kg }}" required>
                            <button type="submit">Save</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('sack-sizes.destroy', $sackSize) }}" onsubmit="return confirm('Delete this sack size?')">
                            @csrf
                            @method('DELETE')
                            <button class="danger" type="submit" @disabled($sackSize->products_count > 0)>Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No sack sizes configured.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
