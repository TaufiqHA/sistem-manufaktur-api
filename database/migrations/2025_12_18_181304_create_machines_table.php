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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();

            $table->string('code');
            $table->string('name');

            $table->string('type'); 
            // POTONG | PLONG | PRESS | LAS | WT | POWDER | QC

            $table->integer('capacity_per_hour');

            $table->string('status'); 
            // IDLE | RUNNING | MAINTENANCE | OFFLINE | DOWNTIME

            $table->json('personnel'); 
            // MachinePersonnel[]

            $table->boolean('is_maintenance')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
