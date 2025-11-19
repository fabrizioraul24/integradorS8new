<?php

namespace App\Http\Controllers;

use App\Services\AiAgentService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAiController extends Controller
{
    public function index(Request $request, AiAgentService $service): View
    {
        $data = $service->generate();

        $stats = [
            'restock' => count($data['restock'] ?? []),
            'alerts_low' => count($data['alerts']['low_stock'] ?? []),
            'alerts_expiring' => count($data['alerts']['expiring'] ?? []),
        ];

        $forecastTop = collect($data['forecast'] ?? [])->sortByDesc('forecast')->take(5);
        $restockTop = collect($data['restock'] ?? [])->sortByDesc('suggested_qty')->take(5);

        $charts = [
            'forecast' => [
                'labels' => $forecastTop->pluck('name')->values(),
                'data' => $forecastTop->pluck('forecast')->values(),
            ],
            'restock' => [
                'labels' => $restockTop->pluck('name')->values(),
                'data' => $restockTop->pluck('suggested_qty')->values(),
            ],
        ];

        return view('dashboard.agente', [
            'data' => $data,
            'stats' => $stats,
            'charts' => $charts,
        ]);
    }

    public function report(AiAgentService $service)
    {
        $data = $service->generate();
        return ReportService::download('reports.agent', [
            'title' => 'Informe Agente Inteligente',
            'generatedAt' => now(),
            'data' => $data,
            'charts' => [
                'forecast' => collect($data['forecast'] ?? [])->sortByDesc('forecast')->take(5),
                'restock' => collect($data['restock'] ?? [])->sortByDesc('suggested_qty')->take(5),
            ],
        ], 'agente-inteligente.pdf');
    }
}
