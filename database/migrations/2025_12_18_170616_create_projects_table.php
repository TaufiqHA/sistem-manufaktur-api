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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('code');
            $table->string('name');
            $table->string('customer');

            $table->date('start_date');
            $table->date('deadline');

            $table->string('status'); // PLANNED | IN_PROGRESS | COMPLETED | ON_HOLD
            $table->integer('progress');

            $table->integer('qty_per_unit');
            $table->integer('procurement_qty');
            $table->integer('total_qty');

            $table->string('unit');

            $table->boolean('is_locked')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
