<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsAudit;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductLot;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ReportService;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransferController extends Controller
{
    use LogsAudit;
    public function index(Request $request): View
    {
        $transfers = Transfer::with([
            'fromWarehouse',
            'toWarehouse',
            'requestedByUser',
            'items.product',
        ])->latest()->paginate(10);

        $stats = [
            'total' => Transfer::count(),
            'pending' => Transfer::where('status', Transfer::STATUS_PENDING)->count(),
            'in_transit' => Transfer::where('status', Transfer::STATUS_IN_TRANSIT)->count(),
            'received' => Transfer::where('status', Transfer::STATUS_RECEIVED)->count(),
        ];

        return view('dashboard.traspasos', [
            'warehouses' => Warehouse::orderBy('name')->get(),
            'transfers' => $transfers,
            'stats' => $stats,
            'statuses' => Transfer::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required', 'exists:warehouses,id', 'different:from_warehouse_id'],
            'expected_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(Transfer::STATUSES)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.requested_qty' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ], [
            'items.required' => 'Debes agregar al menos un producto al traspaso.',
            'items.*.product_id.required' => 'Completa el código del producto.',
        ]);

        $transfer = DB::transaction(function () use ($data, $request) {
            $transfer = Transfer::create([
                'from_warehouse_id' => $data['from_warehouse_id'] ?? null,
                'to_warehouse_id' => $data['to_warehouse_id'],
                'requested_by' => $request->user()?->id ?? auth()->id(),
                'status' => $data['status'] ?? Transfer::STATUS_PENDING,
                'expected_date' => $data['expected_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                TransferItem::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'requested_qty' => $item['requested_qty'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $transfer;
        });

        $this->logAudit($transfer, 'create', [], [
            'from_warehouse_id' => $transfer->from_warehouse_id,
            'to_warehouse_id' => $transfer->to_warehouse_id,
            'status' => $transfer->status,
            'expected_date' => $transfer->expected_date,
            'items_count' => $transfer->items()->count(),
        ], 'Creacion de traspaso');

        return redirect()
            ->route('dashboard.transfers')
            ->with('status', 'Traspaso registrado correctamente.');
    }

    public function report()
    {
        $transfers = Transfer::with([
            'fromWarehouse',
            'toWarehouse',
            'requestedByUser',
            'items.product',
        ])->latest()->get();

        return ReportService::download('reports.transfers', [
            'title' => 'Reporte de traspasos internos',
            'generatedAt' => now(),
            'transfers' => $transfers,
        ], 'reporte-traspasos.pdf');
    }

    public function reportSingle(Transfer $transfer)
    {
        $transfer->load([
            'fromWarehouse',
            'toWarehouse',
            'requestedByUser',
            'items.product',
        ]);

        $transfers = collect([$transfer]);

        return ReportService::download('reports.transfers', [
            'title' => 'Traspaso #' . $transfer->id,
            'generatedAt' => now(),
            'transfers' => $transfers,
        ], "traspaso-{$transfer->id}.pdf");
    }

    public function lookup(Request $request): JsonResponse
    {
        $sku = $request->query('sku');

        if (! $sku) {
            return response()->json(['message' => 'Debes proporcionar un código de producto.'], 422);
        }

        $product = Product::where('sku', $sku)->first();

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        $warehouseId = $request->query('warehouse_id');
        $availableQuantity = $warehouseId
            ? ProductLot::available($product->id, $warehouseId)
            : null;

        return response()->json([
            'product_id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'description' => $product->description,
            'available_quantity' => $availableQuantity,
        ]);
    }
}
