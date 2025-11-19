<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restock_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restock_request_id')->constrained('restock_requests')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('suggested_qty');
            $table->unsignedInteger('current_qty');
            $table->unsignedInteger('min_qty');
            $table->unsignedInteger('max_qty')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restock_request_items');
    }
};

/* Nota: Se almacenan cantidades mínimas/máximas para auditorías de planeación. */

