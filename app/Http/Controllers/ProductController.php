<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LogsAudit;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductLot;
use App\Models\Warehouse;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    use LogsAudit;

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $baseQuery = $this->baseProductsQuery($request);

        $activeProducts = (clone $baseQuery)
            ->where('is_active', true)
            ->paginate(10, ['*'], 'activos_page')
            ->withQueryString();

        $inactiveProducts = (clone $baseQuery)
            ->where('is_active', false)
            ->paginate(10, ['*'], 'inactivos_page')
            ->withQueryString();

        $stats = [
            'catalog' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
        ];

        return view('dashboard.productos', [
            'activeProducts' => $activeProducts,
            'inactiveProducts' => $inactiveProducts,
            'categories' => Category::orderBy('name')->get(),
            'stats' => $stats,
            'search' => $search,
            'categoryId' => $categoryId,
            'laPazWarehouse' => $this->getLaPazWarehouse(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayload($request, true);
        $data['image_path'] = $this->handleImageUpload($request);
        $product = Product::create($data);

        $this->logAudit($product, 'create', [], $product->only([
            'name','sku','category_id','suggested_price_public','price_institutional','is_active'
        ]) + ['total_stock' => $this->totalStock($product)], 'Creacion de producto');

        return redirect()
            ->route('dashboard.products')
            ->with('status', 'Producto registrado correctamente.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatePayload($request, false, $product->id);
        $data['image_path'] = $this->handleImageUpload($request, $product);

        $old = $product->only(['name','sku','category_id','suggested_price_public','price_institutional','is_active']);
        $old['total_stock'] = $this->totalStock($product);

        $product->update($data);

        $new = $product->only(['name','sku','category_id','suggested_price_public','price_institutional','is_active']);
        $new['total_stock'] = $this->totalStock($product);

        $this->logAudit($product, 'update', $old, $new, 'Actualizacion de producto');

        return redirect()
            ->route('dashboard.products')
            ->with('status', 'Producto actualizado.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $old = $product->only(['name','sku','category_id','suggested_price_public','price_institutional','is_active']);
        $old['total_stock'] = $this->totalStock($product);

        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        $message = $product->is_active
            ? 'Producto reactivado.'
            : 'Producto desactivado.';

        $new = $product->only(['name','sku','category_id','suggested_price_public','price_institutional','is_active']);
        $new['total_stock'] = $this->totalStock($product);

        $this->logAudit(
            $product,
            $product->is_active ? 'activate' : 'deactivate',
            $old,
            $new,
            $message
        );

        return redirect()
            ->route('dashboard.products')
            ->with('status', $message);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $old = $product->only(['name','sku','category_id','suggested_price_public','price_institutional','is_active']);
        $old['total_stock'] = $this->totalStock($product);
        $product->delete();

        $this->logAudit($product, 'delete', $old, [], 'Producto eliminado');

        return redirect()
            ->route('dashboard.products')
            ->with('status', 'Producto archivado correctamente.');
    }

    public function report(Request $request)
    {
        $categoryName = null;
        if ($request->filled('category_id')) {
            $categoryName = Category::find($request->input('category_id'))?->name;
        }

        $statusLabel = match ($request->input('status')) {
            'activos' => 'Activos',
            'inactivos' => 'Inactivos',
            default => 'Todos',
        };

        return ReportService::download('reports.products', [
            'title' => 'Reporte de catalogo',
            'generatedAt' => now(),
            'products' => $this->filteredProductsQuery($request)->get(),
            'filters' => [
                'category' => $categoryName,
                'status' => $statusLabel,
            ],
        ], 'reporte-productos.pdf');
    }

    private function validatePayload(Request $request, bool $imageRequired = false, ?int $productId = null): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'description' => ['nullable', 'string'],
            'suggested_price_public' => ['required', 'numeric', 'min:0'],
            'price_institutional' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'max:5120'],
        ]);

        $validated['is_active'] = (bool) $validated['is_active'];
        unset($validated['image']);

        return $validated;
    }

    private function handleImageUpload(Request $request, ?Product $product = null): ?string
    {
        if (! $request->hasFile('image')) {
            return $product?->image_path;
        }

        if ($product && $product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        // Guardar la imagen en storage/app/public/products
        // Esto corresponde a public/storage/products después del enlace simbólico
        $imagePath = $request->file('image')->store('products', 'public');
        
        return $imagePath;
    }

    private function filteredProductsQuery(Request $request): Builder
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $status = $request->input('status');

        $productsQuery = Product::with(['category', 'inventory'])->latest();

        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        if ($status === 'activos') {
            $productsQuery->where('is_active', true);
        } elseif ($status === 'inactivos') {
            $productsQuery->where('is_active', false);
        }

        return $productsQuery;
    }

    private function baseProductsQuery(Request $request): Builder
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $productsQuery = Product::with(['category', 'inventory'])->latest();

        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        return $productsQuery;
    }

    private function syncLaPazInventory(Product $product, int $quantity): void
    {
        $warehouse = $this->getLaPazWarehouse();

        if (! $warehouse) {
            return;
        }

        Inventory::updateOrCreate(
            [
                'warehouse_id' => $warehouse->id,
                'product_id' => $product->id,
            ],
            [
                'quantity' => $quantity,
            ]
        );
    }

    private function getLaPazWarehouse(): ?Warehouse
    {
        return Warehouse::where('code', 'LPZ')
            ->orWhere('city', 'La Paz')
            ->first();
    }

    private function totalStock(Product $product): int
    {
        return (int) $product->inventory()->sum('quantity');
    }
}
