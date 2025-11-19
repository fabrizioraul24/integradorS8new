<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ProductLotMovement;

class ProductLot extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'lote_code',
        'quantity',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ProductLotMovement::class, 'lot_id');
    }

    protected static function booted(): void
    {
        static::saved(function (ProductLot $lot) {
            $lot->syncInventory();
        });

        static::deleted(function (ProductLot $lot) {
            $lot->syncInventory();
        });
    }

    public function syncInventory(): void
    {
        $total = static::where('product_id', $this->product_id)
            ->where('warehouse_id', $this->warehouse_id)
            ->sum('quantity');

        DB::table('inventory')->updateOrInsert(
            [
                'product_id' => $this->product_id,
                'warehouse_id' => $this->warehouse_id,
            ],
            [
                'quantity' => $total,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public static function available(int $productId, int $warehouseId): int
    {
        return static::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->sum('quantity');
    }

    public static function consumeFefo(int $productId, int $warehouseId, int $quantity, string $type = 'venta', ?int $userId = null, ?string $note = null): void
    {
        $remaining = $quantity;
        $lots = static::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('quantity', '>', 0)
            ->orderBy('expires_at')
            ->lockForUpdate()
            ->get();

        foreach ($lots as $lot) {
            if ($remaining <= 0) {
                break;
            }
            $take = min($remaining, $lot->quantity);
            $lot->quantity = $lot->quantity - $take;
            $lot->save();
            ProductLotMovement::create([
                'lot_id' => $lot->id,
                'user_id' => $userId,
                'type' => $type,
                'quantity' => -$take,
                'note' => $note,
            ]);
            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new \RuntimeException('No hay stock suficiente por lotes.');
        }
    }

    public static function addStock(int $productId, int $warehouseId, int $quantity, ?string $loteCode, \Carbon\Carbon|string $expiresAt, string $type = 'ingreso', ?int $userId = null, ?string $note = null): ProductLot
    {
        $lot = static::create([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'lote_code' => $loteCode,
            'quantity' => $quantity,
            'expires_at' => $expiresAt,
        ]);

        ProductLotMovement::create([
            'lot_id' => $lot->id,
            'user_id' => $userId,
            'type' => $type,
            'quantity' => $quantity,
            'note' => $note,
        ]);

        return $lot;
    }
}
