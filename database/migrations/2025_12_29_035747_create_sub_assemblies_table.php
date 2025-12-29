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
        Schema::create('sub_assemblies', function (Blueprint $table) {
            $table->id();
            $table->string('item_id');
            $table->string('name');
            $table->integer('qty_per_parent');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->json('processes');
            $table->integer('total_needed');
            $table->integer('completed_qty')->default(0);
            $table->integer('total_produced')->default(0);
            $table->integer('consumed_qty')->default(0);
            $table->json('step_stats')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_assemblies');
    }
};
