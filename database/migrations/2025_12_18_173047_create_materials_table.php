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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();

            $table->string('code');
            $table->string('name');
            $table->string('unit');

            $table->integer('current_stock');
            $table->integer('safety_stock');

            $table->decimal('price_per_unit', 15, 2);

            $table->string('category'); // RAW | FINISHING | HARDWARE

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
