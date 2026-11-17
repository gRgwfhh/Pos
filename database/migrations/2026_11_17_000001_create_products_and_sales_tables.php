<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('unit');
            $table->decimal('price', 10, 2);
            $table->decimal('stock', 10, 2)->default(0);
            $table->timestamps();
            $table->index(['type', 'unit']);
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('receipt_number')->index();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestampTz('sale_date')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
        Schema::dropIfExists('products');
    }
};
