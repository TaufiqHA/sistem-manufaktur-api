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
        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->string('task_id');
            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');

            $table->string('machine_id');
            $table->foreign('machine_id')
                ->references('id')
                ->on('machines')
                ->onDelete('restrict');

            $table->string('item_id');
            $table->foreign('item_id')
                ->references('id')
                ->on('project_items')
                ->onDelete('cascade');

            $table->string('project_id');
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            // Process info
            $table->string('step'); // ProcessStep
            $table->string('shift'); // SHIFT_1 | SHIFT_2 | SHIFT_3

            // Quantities
            $table->integer('good_qty')->default(0);
            $table->integer('defect_qty')->default(0);

            // Operator (sementara string, bisa dinormalisasi ke users)
            $table->string('operator');

            // Event
            $table->timestamp('logged_at');
            $table->string('type'); 
            // OUTPUT | DOWNTIME_START | DOWNTIME_END

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_logs');
    }
};
