<?php

namespace App\Http\Controllers;

use App\Models\SackSize;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SackSizeController extends Controller
{
    public function index(): View
    {
        return view('sack-sizes.index', [
            'sackSizes' => SackSize::withCount('products')->orderBy('weight_kg')->get(),
        ]);
    }

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        DB::transaction(function () use ($request, $auditLogger): void {
            $sackSize = SackSize::create($this->validated($request));
            $auditLogger->record(
                $request->user(),
                'inventory.sack_size_created',
                "Added a {$sackSize->weight_kg} kg sack-size option.",
                $sackSize,
                ['weight_kg' => $sackSize->weight_kg],
            );
        });

        return redirect()->route('sack-sizes.index')->with('success', 'Sack size added.');
    }

    public function update(Request $request, SackSize $sackSize, AuditLogger $auditLogger): RedirectResponse
    {
        DB::transaction(function () use ($request, $sackSize, $auditLogger): void {
            $before = $sackSize->weight_kg;
            $sackSize->update($this->validated($request, $sackSize));

            if ($before !== $sackSize->weight_kg) {
                $auditLogger->record(
                    $request->user(),
                    'inventory.sack_size_updated',
                    "Changed a sack-size option from {$before} kg to {$sackSize->weight_kg} kg.",
                    $sackSize,
                    ['before_weight_kg' => $before, 'after_weight_kg' => $sackSize->weight_kg],
                );
            }
        });

        return redirect()->route('sack-sizes.index')->with('success', 'Sack size updated.');
    }

    public function destroy(SackSize $sackSize, Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        if ($sackSize->products()->exists()) {
            return back()->withErrors([
                'sack_size' => 'This size is assigned to products. Reassign them before deleting it.',
            ]);
        }

        DB::transaction(function () use ($sackSize, $request, $auditLogger): void {
            $auditLogger->record(
                $request->user(),
                'inventory.sack_size_deleted',
                "Removed the {$sackSize->weight_kg} kg sack-size option.",
                $sackSize,
                ['weight_kg' => $sackSize->weight_kg],
            );
            $sackSize->delete();
        });

        return redirect()->route('sack-sizes.index')->with('success', 'Sack size deleted.');
    }

    private function validated(Request $request, ?SackSize $sackSize = null): array
    {
        return $request->validate([
            'weight_kg' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999.99',
                Rule::unique('sack_sizes', 'weight_kg')->ignore($sackSize),
            ],
        ]);
    }
}
