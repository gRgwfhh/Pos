<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sack_sizes', function (Blueprint $table) {
            $table->id();
            $table->decimal('weight_kg', 8, 2)->unique();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('sack_size_id')
                ->nullable()
                ->after('unit')
                ->constrained('sack_sizes')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sack_size_id');
        });

        Schema::dropIfExists('sack_sizes');
    }
};
