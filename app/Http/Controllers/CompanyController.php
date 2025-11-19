<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\ReportService;
use App\Http\Controllers\Concerns\LogsAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyController extends Controller
{
    use LogsAudit;
    /**
     * Vista principal para empresas institucionales y tiendas de barrio.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $typeFilter = $request->input('type');

        $baseQuery = Company::withTrashed()->with('creator')->latest();

        $applyFilters = function ($query) use ($search, $typeFilter) {
            if ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('nit', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('owner_first_name', 'like', "%{$search}%")
                        ->orWhere('owner_last_name_paterno', 'like', "%{$search}%");
                });
            }

            if ($typeFilter) {
                $query->where('company_type', $typeFilter);
            }
        };

        $activeCompanies = (clone $baseQuery)
            ->whereNull('deleted_at')
            ->tap($applyFilters)
            ->paginate(10, ['*'], 'activos_page')
            ->withQueryString();

        $inactiveCompanies = (clone $baseQuery)
            ->onlyTrashed()
            ->tap($applyFilters)
            ->paginate(10, ['*'], 'inactivos_page')
            ->withQueryString();

        $stats = [
            'total' => Company::withTrashed()->count(),
            'active' => Company::count(),
            'inactive' => Company::onlyTrashed()->count(),
            'institutional' => Company::where('company_type', 'empresa_institucional')->count(),
            'retail' => Company::where('company_type', 'tienda_barrio')->count(),
        ];

        return view('dashboard.clientes', [
            'activeCompanies' => $activeCompanies,
            'inactiveCompanies' => $inactiveCompanies,
            'search' => $search,
            'typeFilter' => $typeFilter,
            'stats' => $stats,
            'companyTypes' => Company::TYPES,
        ]);
    }

    /**
     * Registrar un nuevo cliente empresarial o tienda de barrio.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayload($request);
        $data['created_by'] = $request->user()?->id ?? auth()->id();

        $company = Company::create($data);

        $this->logAudit($company, 'create', [], $company->only([
            'name','nit','company_type','email','phone','city','created_by'
        ]), 'Creación de cliente');

        return redirect()
            ->route('dashboard.companies')
            ->with('status', 'Cliente registrado correctamente.');
    }

    /**
     * Actualizar un registro existente.
     */
    public function update(Request $request, Company $company): RedirectResponse
    {
        $data = $this->validatePayload($request, $company->id);
        $old = $company->only(['name','nit','company_type','email','phone','city']);
        $company->update($data);

        $this->logAudit($company, 'update', $old, $company->only(['name','nit','company_type','email','phone','city']), 'Actualización de cliente');

        return redirect()
            ->route('dashboard.companies')
            ->with('status', 'Cliente actualizado.');
    }

    /**
     * Enviar a papelera mediante soft delete.
     */
    public function destroy(Company $company): RedirectResponse
    {
        $old = $company->only(['name','nit','company_type','email','phone','city']);
        $company->delete();

        $this->logAudit($company, 'deactivate', $old, [], 'Desactivación de cliente');

        return redirect()
            ->route('dashboard.companies')
            ->with('status', 'Cliente desactivado correctamente.');
    }

    /**
     * Reactivar un cliente desactivado.
     */
    public function restore(int $companyId): RedirectResponse
    {
        $company = Company::withTrashed()->findOrFail($companyId);
        $new = $company->only(['name','nit','company_type','email','phone','city']);
        $company->restore();

        $this->logAudit($company, 'restore', [], $new, 'Reactivación de cliente');

        return redirect()
            ->route('dashboard.companies')
            ->with('status', 'Cliente reactivado correctamente.');
    }

    /**
     * Descargar reporte PDF.
     */
    public function report(Request $request)
    {
        $search = $request->input('search');
        $typeFilter = $request->input('type');

        $companiesQuery = Company::query()->with('creator')->latest();

        if ($search) {
            $companiesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nit', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('owner_first_name', 'like', "%{$search}%")
                    ->orWhere('owner_last_name_paterno', 'like', "%{$search}%");
            });
        }

        if ($typeFilter) {
            $companiesQuery->where('company_type', $typeFilter);
        }

        return ReportService::download('reports.companies', [
            'title' => 'Reporte de clientes empresariales',
            'generatedAt' => now(),
            'companies' => $companiesQuery->get(),
            'companyTypes' => Company::TYPES,
            'filters' => [
                'type_label' => $typeFilter ? (Company::TYPES[$typeFilter] ?? null) : null,
            ],
        ], 'reporte-clientes.pdf');
    }

    /**
     * Validacion reutilizable para store/update.
     */
    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'company_type' => ['required', Rule::in(array_keys(Company::TYPES))],
            'name' => ['required', 'string', 'max:255'],
            'nit' => [
                'required',
                'string',
                'max:100',
                Rule::unique('companies', 'nit')->ignore($ignoreId),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'google_maps_url' => ['nullable', 'url', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'owner_first_name' => ['required', 'string', 'max:255'],
            'owner_last_name_paterno' => ['required', 'string', 'max:255'],
            'owner_last_name_materno' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
