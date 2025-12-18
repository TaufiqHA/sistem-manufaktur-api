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
        Schema::create('project_items', function (Blueprint $table) {
            $table->id();

            $table->string('project_id');
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->string('name');
            $table->string('dimensions');
            $table->string('thickness');

            $table->integer('qty_set');
            $table->integer('quantity');

            $table->string('unit');

            $table->boolean('is_bom_locked')->default(false);
            $table->boolean('is_workflow_locked')->default(false);

            $table->json('workflow'); 
            // ItemStepConfig[]

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_items');
    }
};
