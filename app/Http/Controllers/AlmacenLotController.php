<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductLot;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlmacenLotController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $lotsQuery = ProductLot::with(['product', 'warehouse'])
            ->when($filters['product_id'] ?? null, fn ($q, $productId) => $q->where('product_id', $productId))
            ->when($filters['warehouse_id'] ?? null, fn ($q, $warehouseId) => $q->where('warehouse_id', $warehouseId))
            ->when($filters['expires_at'] ?? null, fn ($q, $date) => $q->whereDate('expires_at', $date))
            ->orderBy('expires_at');

        $stats = [
            'lots' => ProductLot::count(),
            'stock' => ProductLot::sum('quantity'),
            'expiring' => ProductLot::whereBetween('expires_at', [now(), now()->addDays(30)])->count(),
        ];

        return view('dashboard.almacen-lotes', [
            'lots' => $lotsQuery->paginate(10)->withQueryString(),
            'products' => Product::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('name')->get(),
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }
}
