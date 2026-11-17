<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->foreignId('role_id')->constrained()->restrictOnDelete()->after('password');
            $table->dropUnique(['email']);
            $table->dropColumn(['name', 'email', 'email_verified_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'role_id']);
            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });

        Schema::dropIfExists('roles');
    }
};
