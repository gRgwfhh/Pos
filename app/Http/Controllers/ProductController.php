<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SackSize;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const TYPES = ['rice', 'cat food', 'dog food', 'chicken feed', 'pig feed'];

    public function index(): View
    {
        return view('products.index', [
            'products' => Product::with('sackSize')
                ->orderBy('type')
                ->orderBy('unit')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('products.form', [
            'product' => new Product,
            'types' => self::TYPES,
            'sackSizes' => SackSize::orderBy('weight_kg')->get(),
        ]);
    }

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $product = DB::transaction(function () use ($request, $auditLogger): Product {
            $product = Product::create($this->validated($request));
            $auditLogger->record(
                $request->user(),
                'inventory.product_created',
                "Created product {$product->name}.",
                $product,
                ['product' => $this->productSnapshot($product)],
            );

            return $product;
        });

        return redirect()->route('products.index')->with('success', 'Product added.');
    }

    public function edit(Product $product): View
    {
        return view('products.form', [
            'product' => $product,
            'types' => self::TYPES,
            'sackSizes' => SackSize::orderBy('weight_kg')->get(),
        ]);
    }

    public function update(Request $request, Product $product, AuditLogger $auditLogger): RedirectResponse
    {
        DB::transaction(function () use ($request, $product, $auditLogger): void {
            $before = $this->productSnapshot($product);
            $product->update($this->validated($request));
            $after = $this->productSnapshot($product);

            if ($before !== $after) {
                $auditLogger->record(
                    $request->user(),
                    'inventory.product_updated',
                    "Updated product {$product->name}.",
                    $product,
                    ['before' => $before, 'after' => $after],
                );
            }
        });

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product, Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        if ($product->sales()->exists()) {
            return back()->withErrors(['product' => 'Products with sales cannot be deleted.']);
        }

        DB::transaction(function () use ($product, $request, $auditLogger): void {
            $auditLogger->record(
                $request->user(),
                'inventory.product_deleted',
                "Deleted product {$product->name}.",
                $product,
                ['product' => $this->productSnapshot($product)],
            );
            $product->delete();
        });

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', self::TYPES)],
            'unit' => ['required', 'in:kilo,sack'],
            'sack_size_id' => [
                Rule::requiredIf($request->input('unit') === 'kilo'),
                'nullable',
                'integer',
                'exists:sack_sizes,id',
            ],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'stock' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        if ($validated['unit'] === 'sack') {
            $validated['sack_size_id'] = null;
        }

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    private function productSnapshot(Product $product): array
    {
        return $product->only(['name', 'type', 'unit', 'sack_size_id', 'price', 'stock']);
    }
}
