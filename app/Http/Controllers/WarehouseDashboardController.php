<?php

namespace App\Http\Controllers;

use App\Models\ProductLot;
use App\Models\Sale;
use App\Models\Transfer;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WarehouseDashboardController extends Controller
{
    public function __invoke(): View
    {
        $recentTransfers = Transfer::with(['fromWarehouse', 'toWarehouse'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'stock' => ProductLot::sum('quantity'),
            'pending_orders' => Sale::where('status', 'sin_entregar')->count(),
            'transfers_today' => Transfer::whereDate('created_at', today())->count(),
            'expiring_lots' => ProductLot::whereBetween('expires_at', [now(), now()->addDays(30)])->count(),
        ];

        $capacityData = Warehouse::select('warehouses.id', 'warehouses.name', 'warehouses.capacity_max')
            ->leftJoin('product_lots', 'product_lots.warehouse_id', '=', 'warehouses.id')
            ->selectRaw('COALESCE(SUM(product_lots.quantity), 0) as occupancy')
            ->groupBy('warehouses.id', 'warehouses.name', 'warehouses.capacity_max')
            ->get()
            ->map(function ($row) {
                $capacity = max(1, $row->capacity_max ?? 1);
                $row->percent = min(100, round(($row->occupancy / $capacity) * 100, 1));
                return $row;
            });

        $lastDays = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));
        $transferSeries = $lastDays->map(function ($day) {
            return [
                'label' => $day->format('d/m'),
                'count' => Transfer::whereDate('created_at', $day)->count(),
            ];
        });

        return view('dashboard.almacen', [
            'recentTransfers' => $recentTransfers,
            'stats' => $stats,
            'capacityChart' => [
                'labels' => $capacityData->pluck('name'),
                'data' => $capacityData->pluck('percent'),
            ],
            'transferSeries' => [
                'labels' => $transferSeries->pluck('label'),
                'data' => $transferSeries->pluck('count'),
            ],
        ]);
    }
}
