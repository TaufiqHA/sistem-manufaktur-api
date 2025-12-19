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
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->unique();
            $table->string('path');
            $table->string('disk')->default('local'); // storage disk where backup is saved
            $table->unsignedBigInteger('size')->nullable(); // file size in bytes
            $table->string('status')->default('completed'); // pending, processing, completed, failed
            $table->string('type')->default('full'); // full, incremental, selective
            $table->json('details')->nullable(); // additional info about the backup
            $table->timestamp('completed_at')->nullable();
            $table->string('created_by')->nullable(); // user who initiated the backup
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
