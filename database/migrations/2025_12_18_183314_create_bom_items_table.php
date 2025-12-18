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
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();

            $table->string('item_id');
            $table->foreign('item_id')
                ->references('id')
                ->on('project_items')
                ->onDelete('cascade');

            $table->string('material_id');
            $table->foreign('material_id')
                ->references('id')
                ->on('materials')
                ->onDelete('restrict');

            $table->integer('quantity_per_unit');
            $table->integer('total_required');

            $table->integer('allocated');
            $table->integer('realized');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};
