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
        // First, drop foreign key constraints
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['item_id']);
            $table->dropForeign(['machine_id']);
        });

        // Change column types from string to integer
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->change();
            $table->unsignedBigInteger('item_id')->change();
            $table->unsignedBigInteger('machine_id')->change();
        });

        // Re-add foreign key constraints
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('item_id')
                ->references('id')
                ->on('project_items')
                ->onDelete('cascade');

            $table->foreign('machine_id')
                ->references('id')
                ->on('machines')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, drop foreign key constraints
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['item_id']);
            $table->dropForeign(['machine_id']);
        });

        // Change column types back to string
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('project_id')->change();
            $table->string('item_id')->change();
            $table->string('machine_id')->change();
        });

        // Re-add foreign key constraints
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('item_id')
                ->references('id')
                ->on('project_items')
                ->onDelete('cascade');

            $table->foreign('machine_id')
                ->references('id')
                ->on('machines')
                ->onDelete('restrict');
        });
    }
};