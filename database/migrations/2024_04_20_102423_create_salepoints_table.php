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
        Schema::create('salepoints', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('type')->default('');
            $table->string('name')->default('');;
            $table->string('address')->default('');;
            $table->text('openingHours')->default('');
            $table->decimal('lat', total: 9, places: 7);
            $table->decimal('lon', total: 9, places: 7);
            $table->unsignedInteger('services');
            $table->unsignedSmallInteger('payMethods');
            $table->string('link')->default('');
            $table->text('remarks')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salepoints');
    }
};
