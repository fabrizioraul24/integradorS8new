<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductLot;
use App\Models\ProductLotMovement;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductLotController extends Controller
{
    public function index(Request $request): View
    {
        $productId = $request->input('product_id');
        $warehouseId = $request->input('warehouse_id');
        $expires = $request->input('expires_at');

        $laPaz = $this->getLaPazWarehouse();

        $lotsQuery = ProductLot::with(['product', 'warehouse'])
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($laPaz, fn($q) => $q->where('warehouse_id', $laPaz->id))
            ->when($expires, fn($q) => $q->whereDate('expires_at', $expires))
            ->orderBy('expires_at');

        return view('dashboard.lotes', [
            'lots' => $lotsQuery->paginate(10)->withQueryString(),
            'products' => Product::orderBy('name')->get(),
            'warehouses' => $laPaz ? collect([$laPaz]) : Warehouse::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $laPaz = $this->getLaPazWarehouse();

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'lote_code' => ['nullable', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'expires_at' => ['required', 'date'],
        ]);

        if (! $laPaz) {
            return back()->withErrors(['warehouse_id' => 'No existe la bodega de La Paz configurada.'])->withInput();
        }

        $lot = ProductLot::addStock(
            $data['product_id'],
            $laPaz->id,
            $data['quantity'],
            $data['lote_code'],
            $data['expires_at'],
            'ingreso',
            $request->user()?->id,
            'Alta manual de lote'
        );

        return back()->with('status', "Lote #{$lot->id} creado.");
    }

    public function adjust(Request $request, ProductLot $lot): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer'],
            'lote_code' => ['nullable', 'string', 'max:100'],
            'expires_at' => ['required', 'date'],
        ]);

        $previous = $lot->quantity;
        $lot->lote_code = $data['lote_code'] ?? $lot->lote_code;
        $lot->expires_at = $data['expires_at'];
        $lot->quantity = max(0, $data['quantity']);
        $lot->save();

        ProductLotMovement::create([
            'lot_id' => $lot->id,
            'user_id' => $request->user()?->id,
            'type' => 'ajuste',
            'quantity' => $lot->quantity - $previous,
            'note' => 'Ajuste lote',
        ]);

        return back()->with('status', 'Ajuste registrado.');
    }

    private function getLaPazWarehouse(): ?\App\Models\Warehouse
    {
        return Warehouse::where('code', 'LPZ')
            ->orWhere('city', 'La Paz')
            ->first();
    }
}
