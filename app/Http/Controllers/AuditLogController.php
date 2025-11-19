<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $actorId = $request->input('actor_id');
        $entityType = $request->input('entity_type');
        $action = $request->input('action');
        $scope = $request->input('scope', 'all'); // all, login, register, users

        $logsQuery = AuditLog::with('user')->latest('created_at');

        if ($scope === 'login') {
            $logsQuery->where('entity_type', 'auth')
                ->whereIn('action', ['login', 'login_failed', 'logout']);
        } elseif ($scope === 'register') {
            $logsQuery->where('entity_type', 'auth')
                ->whereIn('action', ['register', 'register_failed']);
        } elseif ($scope === 'users') {
            $logsQuery->where('entity_type', User::class);
        } elseif ($scope === 'customers') {
            $logsQuery->where('entity_type', Company::class);
        } elseif ($scope === 'products') {
            $logsQuery->where('entity_type', Product::class);
        } elseif ($scope === 'categories') {
            $logsQuery->where('entity_type', Category::class);
        } elseif ($scope === 'transfers') {
            $logsQuery->where('entity_type', Transfer::class);
        }

        if ($actorId) {
            $logsQuery->where('user_id', $actorId);
        }

        if ($entityType) {
            $logsQuery->where('entity_type', $entityType);
        }

        if ($action) {
            $logsQuery->where('action', $action);
        }

        $logs = $logsQuery->paginate(15)->withQueryString();

        return view('dashboard.logs', [
            'logs' => $logs,
            'actors' => User::orderBy('name')->get(),
            'entityTypes' => AuditLog::query()->select('entity_type')->distinct()->pluck('entity_type'),
            'actions' => AuditLog::query()->select('action')->distinct()->pluck('action'),
            'filters' => [
                'actor_id' => $actorId,
                'entity_type' => $entityType,
                'action' => $action,
                'scope' => $scope,
            ],
        ]);
    }
}
