<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();

        $salesToday = (float) Sale::whereDate('created_at', $today)->sum('total_amount');

        $kpis = [
            'sales_today' => $salesToday,
            'customers' => Company::count(),
            'products_active' => Product::where('is_active', true)->count(),
            'transfers_active' => Transfer::where('status', '!=', Transfer::STATUS_RECEIVED)->count(),
        ];

        $dates = collect(range(6, 0))->map(fn ($i) => $today->copy()->subDays($i));
        $rawSales = Sale::select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(total_amount) as total'))
            ->whereDate('created_at', '>=', $today->copy()->subDays(6))
            ->groupBy('day')
            ->pluck('total', 'day');
        $salesSeries = [
            'labels' => $dates->map->format('d/m'),
            'data' => $dates->map(fn ($d) => (float) ($rawSales[$d->toDateString()] ?? 0)),
        ];

        $categoryMixData = Product::select('category_id', DB::raw('COUNT(*) as total'))
            ->with('category')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take(6)
            ->get();
        $categoryMix = [
            'labels' => $categoryMixData->map(fn ($row) => $row->category->name ?? 'Sin categoria'),
            'data' => $categoryMixData->pluck('total')->map(fn ($v) => (int) $v),
        ];

        $transferStatusData = Transfer::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $transferStatuses = [
            'labels' => $transferStatusData->keys(),
            'data' => $transferStatusData->values()->map(fn ($v) => (int) $v),
        ];

        $usersByRole = User::select('role_id', DB::raw('COUNT(*) as total'))
            ->with('role')
            ->groupBy('role_id')
            ->get();
        $roleMix = [
            'labels' => $usersByRole->map(fn ($row) => $row->role->name ?? 'Sin rol'),
            'data' => $usersByRole->pluck('total')->map(fn ($v) => (int) $v),
        ];

        return view('dashboard.admin', [
            'kpis' => $kpis,
            'salesSeries' => $salesSeries,
            'categoryMix' => $categoryMix,
            'transferStatuses' => $transferStatuses,
            'roleMix' => $roleMix,
        ]);
    }
}
