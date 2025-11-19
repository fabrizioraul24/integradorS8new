<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['sku' => '1001', 'name' => 'Leche UHT Entera 1L', 'category' => 'Leche UHT entera', 'description' => 'Leche larga vida entera de un litro.', 'suggested_price_public' => 8.50, 'price_institutional' => 7.20, 'stock' => ['LPZ' => 150, 'SCZ' => 120]],
            ['sku' => '1002', 'name' => 'Leche UHT Entera 500ml', 'category' => 'Leche UHT entera', 'description' => 'Presentación mediana de leche UHT entera.', 'suggested_price_public' => 5.00, 'price_institutional' => 4.30, 'stock' => ['LPZ' => 140, 'SCZ' => 110]],
            ['sku' => '1003', 'name' => 'Leche UHT Descremada 1L', 'category' => 'Leche UHT descremada', 'description' => 'Leche UHT descremada formato familiar.', 'suggested_price_public' => 8.50, 'price_institutional' => 7.20, 'stock' => ['LPZ' => 130, 'SCZ' => 100]],
            ['sku' => '1004', 'name' => 'Leche UHT Descremada 500ml', 'category' => 'Leche UHT descremada', 'description' => 'Leche UHT descremada para consumo individual.', 'suggested_price_public' => 5.00, 'price_institutional' => 4.30, 'stock' => ['LPZ' => 120, 'SCZ' => 90]],
            ['sku' => '1005', 'name' => 'Leche Fresca Pasteurizada 1L', 'category' => 'Leche fresca pasteurizada', 'description' => 'Leche refrigerada pasteurizada lista para el desayuno.', 'suggested_price_public' => 7.80, 'price_institutional' => 6.60, 'stock' => ['LPZ' => 110, 'SCZ' => 85]],
            ['sku' => '1006', 'name' => 'Leche Fresca Pasteurizada 500ml', 'category' => 'Leche fresca pasteurizada', 'description' => 'Envase medio refrigerado ideal para consumo diario.', 'suggested_price_public' => 4.30, 'price_institutional' => 3.70, 'stock' => ['LPZ' => 105, 'SCZ' => 80]],
            ['sku' => '1007', 'name' => 'Yogurt Bebible Frutilla 1L', 'category' => 'Yogurt bebible', 'description' => 'Yogurt bebible sabor frutilla para toda la familia.', 'suggested_price_public' => 12.00, 'price_institutional' => 10.20, 'stock' => ['LPZ' => 95, 'SCZ' => 70]],
            ['sku' => '1008', 'name' => 'Yogurt Bebible Durazno 1L', 'category' => 'Yogurt bebible', 'description' => 'Yogurt bebible sabor durazno con probióticos.', 'suggested_price_public' => 12.00, 'price_institutional' => 10.20, 'stock' => ['LPZ' => 95, 'SCZ' => 70]],
            ['sku' => '1009', 'name' => 'Yogurt Bebible Natural 500ml', 'category' => 'Yogurt bebible', 'description' => 'Yogurt bebible natural sin azúcar añadida.', 'suggested_price_public' => 6.90, 'price_institutional' => 5.90, 'stock' => ['LPZ' => 90, 'SCZ' => 65]],
            ['sku' => '1010', 'name' => 'Yogurt Griego Natural 150g', 'category' => 'Yogurt griego', 'description' => 'Yogurt griego cremoso natural alto en proteína.', 'suggested_price_public' => 5.50, 'price_institutional' => 4.70, 'stock' => ['LPZ' => 80, 'SCZ' => 60]],
            ['sku' => '1011', 'name' => 'Yogurt Griego Frutilla 150g', 'category' => 'Yogurt griego', 'description' => 'Yogurt griego con trozos de frutilla.', 'suggested_price_public' => 5.90, 'price_institutional' => 5.00, 'stock' => ['LPZ' => 80, 'SCZ' => 60]],
            ['sku' => '1012', 'name' => 'Queso Fresco Criollo 500g', 'category' => 'Queso fresco', 'description' => 'Queso fresco criollo semidescremado.', 'suggested_price_public' => 20.00, 'price_institutional' => 17.50, 'stock' => ['LPZ' => 75, 'SCZ' => 55]],
            ['sku' => '1013', 'name' => 'Queso Fresco Campesino 1kg', 'category' => 'Queso fresco', 'description' => 'Queso fresco campesino para mesa y cocina.', 'suggested_price_public' => 36.00, 'price_institutional' => 31.00, 'stock' => ['LPZ' => 70, 'SCZ' => 50]],
            ['sku' => '1014', 'name' => 'Queso Maduro Edam 400g', 'category' => 'Queso maduro', 'description' => 'Queso maduro tipo Edam de sabor suave.', 'suggested_price_public' => 38.00, 'price_institutional' => 33.00, 'stock' => ['LPZ' => 65, 'SCZ' => 45]],
            ['sku' => '1015', 'name' => 'Queso Maduro Gouda 300g', 'category' => 'Queso maduro', 'description' => 'Queso maduro tipo Gouda con notas caramelizadas.', 'suggested_price_public' => 34.00, 'price_institutional' => 29.00, 'stock' => ['LPZ' => 65, 'SCZ' => 45]],
            ['sku' => '1016', 'name' => 'Mantequilla Tradicional 200g', 'category' => 'Mantequilla', 'description' => 'Mantequilla con sal para cocinar y untar.', 'suggested_price_public' => 10.50, 'price_institutional' => 9.00, 'stock' => ['LPZ' => 120, 'SCZ' => 90]],
            ['sku' => '1017', 'name' => 'Mantequilla Sin Sal 200g', 'category' => 'Mantequilla', 'description' => 'Mantequilla sin sal ideal para repostería.', 'suggested_price_public' => 10.50, 'price_institutional' => 9.00, 'stock' => ['LPZ' => 110, 'SCZ' => 85]],
            ['sku' => '1018', 'name' => 'Crema de Leche 200ml', 'category' => 'Crema de leche', 'description' => 'Crema pasteurizada para cocina diaria.', 'suggested_price_public' => 7.50, 'price_institutional' => 6.30, 'stock' => ['LPZ' => 115, 'SCZ' => 90]],
            ['sku' => '1019', 'name' => 'Crema para Batir 1L', 'category' => 'Crema de leche', 'description' => 'Crema de leche con alto contenido de grasa para batir.', 'suggested_price_public' => 27.00, 'price_institutional' => 23.00, 'stock' => ['LPZ' => 70, 'SCZ' => 55]],
            ['sku' => '1020', 'name' => 'Flan de Vainilla 120g', 'category' => 'Postres lacteos', 'description' => 'Postre lácteo sabor vainilla listo para servir.', 'suggested_price_public' => 4.20, 'price_institutional' => 3.60, 'stock' => ['LPZ' => 100, 'SCZ' => 80]],
        ];

        $categoryCache = [];
        $warehouses = Warehouse::whereIn('code', ['LPZ', 'SCZ', 'CBA'])->get()->keyBy('code');

        foreach ($products as $productData) {
            $categoryName = $productData['category'];
            $categoryId = $categoryCache[$categoryName] ?? Category::firstOrCreate(
                ['name' => $categoryName],
                ['description' => $categoryName]
            )->id;
            $categoryCache[$categoryName] = $categoryId;

            $product = Product::updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'category_id' => $categoryId,
                    'suggested_price_public' => $productData['suggested_price_public'],
                    'price_institutional' => $productData['price_institutional'],
                    'is_active' => true,
                    'image_path' => $productData['image_path'] ?? null,
                ]
            );

            $stockConfig = $productData['stock'] ?? [];
            if (is_int($stockConfig)) {
                $stockConfig = ['LPZ' => $stockConfig];
            }

            foreach ($stockConfig as $warehouseCode => $quantity) {
                $warehouse = $warehouses[$warehouseCode] ?? null;
                if (! $warehouse || $quantity === null) {
                    continue;
                }

                Inventory::updateOrCreate(
                    [
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => $quantity,
                        'min_quantity' => 0,
                        'max_quantity' => null,
                    ]
                );
            }
        }
    }
}
