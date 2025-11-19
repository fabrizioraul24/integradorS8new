<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->string('payment_method', 50)->nullable()->after('status');
            $table->decimal('amount_received', 10, 2)->nullable()->after('payment_method');
            $table->decimal('change_amount', 10, 2)->nullable()->after('amount_received');
            $table->decimal('total_amount', 10, 2)->default(0)->after('change_amount');
            $table->timestamp('paid_at')->nullable()->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'amount_received',
                'change_amount',
                'total_amount',
                'paid_at',
            ]);
        });
    }
};
