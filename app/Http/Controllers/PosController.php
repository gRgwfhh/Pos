<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        return view('pos.index', [
            'products' => Product::with('sackSize')
                ->where('stock', '>', 0)
                ->orderBy('type')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function checkout(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
        ]);

        $receiptNumber = (string) Str::uuid();
        $saleLines = [];
        $saleTotalCents = 0;
        $firstSale = null;

        DB::transaction(function () use ($validated, $receiptNumber, $request, $auditLogger, &$saleLines, &$saleTotalCents, &$firstSale): void {
            foreach ($validated['items'] as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $quantity = round((float) $item['quantity'], 2);

                if ($quantity <= 0 || $quantity > (float) $product->stock) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for {$product->name}. Available: {$product->stock}.",
                    ]);
                }

                if ((float) $product->price <= 0) {
                    throw ValidationException::withMessages([
                        'items' => "{$product->name} needs a price before it can be sold.",
                    ]);
                }

                $total = round($quantity * (float) $product->price, 2);

                $sale = Sale::create([
                    'receipt_number' => $receiptNumber,
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $total,
                    'sale_date' => now(),
                ]);

                $product->decrement('stock', $quantity);
                $firstSale ??= $sale;
                $saleTotalCents += (int) round($total * 100);
                $saleLines[] = [
                    'product' => $product->name,
                    'quantity' => $quantity,
                    'unit' => $product->unit,
                    'unit_price' => $product->price,
                    'total_price' => number_format($total, 2, '.', ''),
                ];
            }

            $auditLogger->record(
                $request->user(),
                'sale.completed',
                'Completed receipt '.substr($receiptNumber, 0, 8).' for '.count($saleLines).' item(s).',
                $firstSale,
                [
                    'receipt_number' => $receiptNumber,
                    'items' => $saleLines,
                    'total' => number_format($saleTotalCents / 100, 2, '.', ''),
                ],
            );
        });

        return redirect()->route('pos.index')->with('success', "Sale completed. Receipt: {$receiptNumber}");
    }
}
