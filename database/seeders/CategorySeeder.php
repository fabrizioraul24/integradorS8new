<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Leche UHT entera', 'description' => 'Leches larga vida enteras.'],
            ['name' => 'Leche UHT descremada', 'description' => 'Leches larga vida descremadas.'],
            ['name' => 'Leche fresca pasteurizada', 'description' => 'Leches refrigeradas para consumo diario.'],
            ['name' => 'Yogurt bebible', 'description' => 'Presentaciones bebibles para consumo a domicilio.'],
            ['name' => 'Yogurt griego', 'description' => 'Yogurts tipo griego altos en proteinas.'],
            ['name' => 'Queso fresco', 'description' => 'Quesos suaves ideales para mesa y cocina.'],
            ['name' => 'Queso maduro', 'description' => 'Quesos curados con sabores intensos.'],
            ['name' => 'Mantequilla', 'description' => 'Mantequillas para cocina y reposteria.'],
            ['name' => 'Crema de leche', 'description' => 'Cremas para batir o cocinar.'],
            ['name' => 'Postres lacteos', 'description' => 'Postres listos basados en leche.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description'] ?? null]
            );
        }
    }
}
