<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Customer;
use App\Models\DamageReport;
use App\Models\Product;
use App\Models\ProductLot;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        $categories = Category::pluck('id');
        $warehouses = Warehouse::all();
        $cities = City::all();
        $companies = Company::pluck('id');
        $customers = Customer::pluck('id');
        $sellerId = User::first()?->id;

        if ($categories->isEmpty() || $warehouses->isEmpty() || $cities->isEmpty()) {
            $this->command?->warn('No hay datos base suficientes para DemoDataSeeder.');
            return;
        }

        $this->seedProducts($faker, $categories, $warehouses);
        $this->seedSales($faker, $warehouses, $companies, $customers, $cities, $sellerId);
        $this->seedTransfers($faker, $warehouses, $products ?? Product::all(), $sellerId);
        $this->seedDamageReports($faker);
    }

    private function seedProducts($faker, $categories, $warehouses): void
    {
        $existingCount = Product::count();
        $target = max(0, 100 - $existingCount);

        for ($i = 0; $i < $target; $i++) {
            $product = Product::create([
                'category_id' => $categories->random(),
                'name' => $faker->unique()->words(3, true),
                'description' => $faker->sentence(12),
                'sku' => strtoupper(Str::random(8)),
                'suggested_price_public' => $faker->numberBetween(5, 80),
                'price_institutional' => $faker->numberBetween(4, 60),
                'is_active' => true,
                'image_path' => null,
            ]);

            $lotWarehouse = $warehouses->random();
            ProductLot::addStock(
                $product->id,
                $lotWarehouse->id,
                $faker->numberBetween(200, 600),
                strtoupper(Str::random(6)),
                now()->addDays($faker->numberBetween(30, 420))
            );
        }
    }

    private function seedSales($faker, $warehouses, $companies, $customers, $cities, ?int $sellerId): void
    {
        if ($warehouses->isEmpty()) {
            return;
        }

        $products = Product::all();
        if ($products->isEmpty()) {
            return;
        }

        $paymentMethods = ['efectivo', 'qr', 'tarjeta_debito'];
        $existing = Sale::count();
        $targetSales = max(0, 100 - $existing);

        for ($i = 0; $i < $targetSales; $i++) {
            $type = Arr::random(Sale::TYPES);
            $warehouse = $warehouses->random();
            $city = $cities->random();
            $payment = Arr::random($paymentMethods);

            $companyId = null;
            $customerId = null;

            if ($type === 'comprador_minorista' && $customers->isNotEmpty()) {
                $customerId = $customers->random();
            } elseif ($companies->isNotEmpty()) {
                $companyId = $companies->random();
            } else {
                continue;
            }

            $sale = Sale::create([
                'company_id' => $companyId,
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'warehouse_id' => $warehouse->id,
                'sale_type' => $type,
                'delivery_address' => $faker->streetAddress(),
                'delivery_city' => $city->name,
                'delivery_city_id' => $city->id,
                'status' => Arr::random(Sale::STATUSES),
                'payment_method' => $payment,
                'amount_received' => $payment === 'efectivo' ? $faker->numberBetween(200, 1500) : null,
                'change_amount' => null,
                'total_amount' => 0,
            ]);

            $items = $products->shuffle()->take(rand(1, min(4, $products->count())));
            if ($items->isEmpty()) {
                continue;
            }
            $total = 0;

            foreach ($items as $product) {
                $qty = $faker->numberBetween(5, 40);
                $unitPrice = $type === 'empresa_institucional'
                    ? $product->price_institutional
                    : $product->suggested_price_public;

                $subtotal = $qty * $unitPrice;
                $total += $subtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);
            }

            $sale->update([
                'total_amount' => $total,
                'change_amount' => $sale->amount_received ? max(0, $sale->amount_received - $total) : null,
            ]);
        }
    }

    private function seedTransfers($faker, $warehouses, $products, ?int $sellerId): void
    {
        if ($warehouses->count() < 2 || $products->isEmpty()) {
            return;
        }

        $existing = Transfer::count();
        $target = max(0, 60 - $existing);

        for ($i = 0; $i < $target; $i++) {
            $from = $warehouses->random();
            $to = $warehouses->where('id', '!=', $from->id)->random();
            $status = Arr::random(Transfer::STATUSES);

            $transfer = Transfer::create([
                'from_warehouse_id' => $from->id,
                'to_warehouse_id' => $to->id,
                'requested_by' => $sellerId,
                'status' => $status,
                'expected_date' => now()->addDays($faker->numberBetween(2, 15)),
                'received_date' => $status === Transfer::STATUS_RECEIVED ? now()->addDays($faker->numberBetween(1, 3)) : null,
                'notes' => $faker->sentence(),
            ]);

            $items = $products->shuffle()->take(rand(1, min(5, $products->count())));
            foreach ($items as $product) {
                $requested = $faker->numberBetween(20, 120);
                $received = $status === Transfer::STATUS_RECEIVED ? max(0, $requested - $faker->numberBetween(0, 5)) : null;
                $damaged = $status === Transfer::STATUS_RECEIVED ? max(0, $faker->numberBetween(0, 3)) : 0;

                TransferItem::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $product->id,
                    'requested_qty' => $requested,
                    'received_qty' => $status === Transfer::STATUS_RECEIVED ? $received : null,
                    'damaged_qty' => $damaged,
                    'notes' => $faker->optional()->sentence(),
                    'lot_code' => $status === Transfer::STATUS_RECEIVED ? strtoupper(Str::random(6)) : null,
                    'receiving_expires_at' => $status === Transfer::STATUS_RECEIVED ? now()->addDays($faker->numberBetween(30, 300)) : null,
                    'receiving_note' => $status === Transfer::STATUS_RECEIVED ? $faker->optional()->sentence() : null,
                ]);

                if ($status === Transfer::STATUS_RECEIVED && $received) {
                    ProductLot::addStock(
                        $product->id,
                        $to->id,
                        $received,
                        strtoupper(Str::random(6)),
                        now()->addDays($faker->numberBetween(60, 420))
                    );
                }
            }
        }
    }

    private function seedDamageReports($faker): void
    {
        $lots = ProductLot::all();
        if ($lots->isEmpty()) {
            return;
        }

        $existing = DamageReport::count();
        $target = max(0, 40 - $existing);

        for ($i = 0; $i < $target; $i++) {
            $lot = $lots->random();
            $damaged = $faker->numberBetween(1, min(20, $lot->quantity));

            if ($lot->quantity <= 0) {
                continue;
            }

            $lot->quantity = max(0, $lot->quantity - $damaged);
            $lot->save();

            DamageReport::create([
                'product_lot_id' => $lot->id,
                'product_id' => $lot->product_id,
                'warehouse_id' => $lot->warehouse_id,
                'reported_by' => null,
                'damaged_qty' => $damaged,
                'comment' => $faker->sentence(),
            ]);
        }
    }
}
