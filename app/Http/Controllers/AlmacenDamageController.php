<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\ProductLot;
use App\Models\ProductLotMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AlmacenDamageController extends Controller
{
    public function index(): View
    {
        $reports = DamageReport::with(['lot.product', 'warehouse', 'reporter'])
            ->latest()
            ->paginate(10);

        $stats = [
            'reports' => DamageReport::count(),
            'units' => DamageReport::sum('damaged_qty'),
        ];

        return view('dashboard.almacen-danos', [
            'reports' => $reports,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_lot_id' => ['required', 'exists:product_lots,id'],
            'damaged_qty' => ['required', 'integer', 'min:1'],
            'comment' => ['nullable', 'string'],
        ]);

        $lot = ProductLot::with(['product', 'warehouse'])->findOrFail($data['product_lot_id']);

        if ($lot->quantity < $data['damaged_qty']) {
            return back()->withErrors(['damaged_qty' => 'El lote solo tiene ' . $lot->quantity . ' unidades disponibles.'])->withInput();
        }

        DB::transaction(function () use ($data, $lot, $request) {
            $lot->quantity -= $data['damaged_qty'];
            $lot->save();

            DamageReport::create([
                'product_lot_id' => $lot->id,
                'product_id' => $lot->product_id,
                'warehouse_id' => $lot->warehouse_id,
                'reported_by' => $request->user()?->id,
                'damaged_qty' => $data['damaged_qty'],
                'comment' => $data['comment'] ?? null,
            ]);

            ProductLotMovement::create([
                'lot_id' => $lot->id,
                'user_id' => $request->user()?->id,
                'type' => 'danio',
                'quantity' => -$data['damaged_qty'],
                'note' => $data['comment'] ?? 'Ajuste por daño',
            ]);
        });

        return redirect()
            ->route('dashboard.almacen.damages')
            ->with('status', 'Daño registrado y stock ajustado.');
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $lot = ProductLot::with(['product', 'warehouse'])
            ->where('lote_code', $data['code'])
            ->where('quantity', '>', 0)
            ->first();

        if (! $lot) {
            return response()->json(['message' => 'No encontramos un lote activo con ese código.'], 404);
        }

        return response()->json([
            'lot_id' => $lot->id,
            'lot_code' => $lot->lote_code,
            'product' => $lot->product->name ?? 'Producto',
            'sku' => $lot->product->sku ?? 'N/A',
            'quantity' => $lot->quantity,
            'expires_at' => optional($lot->expires_at)->format('Y-m-d'),
            'warehouse' => $lot->warehouse->name ?? 'Almacén',
        ]);
    }
}
