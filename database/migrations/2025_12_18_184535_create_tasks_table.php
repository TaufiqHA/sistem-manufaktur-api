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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Project
            $table->string('project_id');
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->string('project_name');

            // Item
            $table->string('item_id');
            $table->foreign('item_id')
                ->references('id')
                ->on('project_items')
                ->onDelete('cascade');

            $table->string('item_name');

            // Process step & machine
            $table->string('step'); // ProcessStep
            $table->string('machine_id');
            $table->foreign('machine_id')
                ->references('id')
                ->on('machines')
                ->onDelete('restrict');

            // Quantities
            $table->integer('target_qty');
            $table->integer('completed_qty')->default(0);
            $table->integer('defect_qty')->default(0);
            $table->string('shift')->nullable();

            // Status & downtime
            $table->string('status'); 
            // PENDING | IN_PROGRESS | PAUSED | COMPLETED | DOWNTIME

            $table->timestamp('downtime_start')->nullable();
            $table->integer('total_downtime_minutes')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
