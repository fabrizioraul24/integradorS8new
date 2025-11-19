<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsAudit;
use App\Models\Category;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use LogsAudit;

    public function index(Request $request): View
    {
        $search = $request->input('search');

        $baseQuery = Category::withTrashed()->withCount('products')->orderByDesc('created_at');

        $applyFilters = function ($query) use ($search) {
            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }
        };

        return view('dashboard.categorias', [
            'activeCategories' => (clone $baseQuery)
                ->whereNull('deleted_at')
                ->tap($applyFilters)
                ->paginate(10, ['*'], 'activos_page')
                ->withQueryString(),
            'inactiveCategories' => (clone $baseQuery)
                ->onlyTrashed()
                ->tap($applyFilters)
                ->paginate(10, ['*'], 'inactivos_page')
                ->withQueryString(),
            'search' => $search,
            'total' => Category::withTrashed()->count(),
            'withProducts' => Category::has('products')->count(),
            'inactive' => Category::onlyTrashed()->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        $category = Category::create($data);

        $this->logAudit($category, 'create', [], $category->only(['name','description']), 'Creacion de categoria');

        return redirect()->route('dashboard.categories')->with('status', 'Categoria creada correctamente.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string'],
        ]);

        $old = $category->only(['name','description']);
        $category->update($data);

        $this->logAudit($category, 'update', $old, $category->only(['name','description']), 'Actualizacion de categoria');

        return redirect()->route('dashboard.categories')->with('status', 'Categoria actualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $old = $category->only(['name','description']);
        $category->delete();

        $this->logAudit($category, 'deactivate', $old, [], 'Categoria desactivada');

        return redirect()->route('dashboard.categories')->with('status', 'Categoria desactivada.');
    }

    public function restore(int $categoryId): RedirectResponse
    {
        $category = Category::withTrashed()->findOrFail($categoryId);
        $category->restore();

        $this->logAudit($category, 'restore', [], $category->only(['name','description']), 'Categoria reactivada');

        return redirect()->route('dashboard.categories')->with('status', 'Categoria reactivada.');
    }

    public function report(Request $request)
    {
        $search = $request->input('search');
        $categoriesQuery = Category::withCount('products')->orderBy('name');

        if ($search) {
            $categoriesQuery->where('name', 'like', "%{$search}%");
        }

        return ReportService::download('reports.categories', [
            'title' => 'Reporte de categorias',
            'generatedAt' => now(),
            'categories' => $categoriesQuery->get(),
        ], 'reporte-categorias.pdf');
    }
}
