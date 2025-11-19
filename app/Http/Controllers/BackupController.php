<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BackupController extends Controller
{
    public function index(): View
    {
        $backups = Backup::with('creator')->latest()->paginate(10);
        $stats = [
            'total' => Backup::count(),
            'completed' => Backup::where('status', 'completed')->count(),
            'failed' => Backup::where('status', 'failed')->count(),
            'last' => Backup::latest()->first(),
        ];

        return view('dashboard.backups', compact('backups', 'stats'));
    }

    public function store(Request $request, BackupService $service): RedirectResponse
    {
        try {
            $service->create($request->user()?->id);

            return redirect()
                ->route('dashboard.backups')
                ->with('status', 'Backup generado correctamente. Puedes descargarlo cuando lo necesites.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard.backups')
                ->with('error', 'No logramos completar el backup: ' . $e->getMessage());
        }
    }

    public function download(Backup $backup)
    {
        $path = 'backups/' . $backup->file_name;

        if (! Storage::disk($backup->disk)->exists($path)) {
            return redirect()
                ->route('dashboard.backups')
                ->with('error', 'El archivo ya no se encuentra disponible.');
        }

        return Storage::disk($backup->disk)->download($path);
    }

    public function destroy(Backup $backup): RedirectResponse
    {
        $path = 'backups/' . $backup->file_name;
        Storage::disk($backup->disk)->delete($path);
        $backup->delete();

        return redirect()
            ->route('dashboard.backups')
            ->with('status', 'Backup eliminado del historial.');
    }
}
