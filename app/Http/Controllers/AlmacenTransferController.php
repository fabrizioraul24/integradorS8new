<?php

namespace App\Http\Controllers;

use App\Models\ProductLot;
use App\Models\Transfer;
use App\Models\TransferItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlmacenTransferController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $transfers = Transfer::with(['fromWarehouse', 'toWarehouse', 'items.product'])
            ->when($status, fn ($query, $value) => $query->where('status', $value))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.almacen-traspasos', [
            'transfers' => $transfers,
            'statuses' => Transfer::STATUSES,
            'filters' => ['status' => $status],
        ]);
    }

    public function updateItem(Request $request, TransferItem $item): RedirectResponse
    {
        $data = $request->validate([
            'received_qty' => ['required', 'integer', 'min:0'],
            'damaged_qty' => ['nullable', 'integer', 'min:0'],
            'lot_code' => ['nullable', 'string', 'max:120'],
            'receiving_expires_at' => ['nullable', 'date'],
            'receiving_note' => ['nullable', 'string'],
        ]);

        $transfer = $item->transfer()->first();

        DB::transaction(function () use ($item, $data, $transfer, $request) {
            $damagedQty = $data['damaged_qty'] ?? 0;
            $receivedQty = $data['received_qty'];
            $goodQty = max($receivedQty - $damagedQty, 0);
            $prevGoodQty = max(($item->received_qty ?? 0) - ($item->damaged_qty ?? 0), 0);
            $delta = $goodQty - $prevGoodQty;

            $item->update([
                'received_qty' => $receivedQty,
                'damaged_qty' => $damagedQty,
                'lot_code' => $data['lot_code'] ?? null,
                'receiving_expires_at' => $data['receiving_expires_at'] ?? null,
                'receiving_note' => $data['receiving_note'] ?? null,
            ]);

            if ($delta !== 0 && $transfer && $transfer->to_warehouse_id && $item->product_id) {
                if ($delta > 0) {
                    $expiresAt = $data['receiving_expires_at'] ?? now()->addMonths(6);
                    ProductLot::addStock(
                        $item->product_id,
                        $transfer->to_warehouse_id,
                        $delta,
                        $data['lot_code'] ?? null,
                        $expiresAt,
                        'traspaso',
                        $request->user()?->id,
                        'Recepción traspaso #' . $transfer->id
                    );
                } else {
                    ProductLot::consumeFefo(
                        $item->product_id,
                        $transfer->to_warehouse_id,
                        abs($delta),
                        'ajuste_traspaso',
                        $request->user()?->id,
                        'Ajuste traspaso #' . $transfer->id
                    );
                }
            }
        });

        return back()->with('status', 'Detalle de traspaso actualizado.');
    }

    public function updateStatus(Request $request, Transfer $transfer): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Transfer::STATUSES)],
            'notes' => ['nullable', 'string'],
        ]);

        $transfer->update([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? $transfer->notes,
            'received_by' => $data['status'] === Transfer::STATUS_RECEIVED ? $request->user()?->id : $transfer->received_by,
            'received_date' => $data['status'] === Transfer::STATUS_RECEIVED ? now() : $transfer->received_date,
        ]);

        return back()->with('status', 'Estado del traspaso actualizado.');
    }
}
