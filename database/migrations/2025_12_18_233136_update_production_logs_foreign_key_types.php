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
        // This approach won't work with SQLite, so we need to recreate the table
        // First, drop the existing production_logs table
        Schema::dropIfExists('production_logs');

        // Then recreate it with correct integer foreign keys
        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();

            // Relations (using integer foreign keys to match related tables)
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->onDelete('cascade');

            $table->unsignedBigInteger('machine_id');
            $table->foreign('machine_id')
                ->references('id')
                ->on('machines')
                ->onDelete('restrict');

            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')
                ->references('id')
                ->on('project_items')
                ->onDelete('cascade');

            $table->unsignedBigInteger('project_id');
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
        // Drop the table and recreate with string foreign keys
        Schema::dropIfExists('production_logs');

        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();

            // Relations (using string foreign keys as originally intended)
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
};
