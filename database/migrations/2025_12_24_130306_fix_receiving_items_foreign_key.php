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
        // Drop the existing foreign key constraint - Laravel auto-generates constraint names
        Schema::table('receiving_items', function (Blueprint $table) {
            // The auto-generated constraint name would be: receiving_items_receiving_id_foreign
            $table->dropForeign(['receiving_id']);
        });

        // Add the correct foreign key constraint
        Schema::table('receiving_items', function (Blueprint $table) {
            $table->foreign('receiving_id')->references('id')->on('receiving_goods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the corrected foreign key constraint
        Schema::table('receiving_items', function (Blueprint $table) {
            $table->dropForeign(['receiving_id']);
        });

        // Add back the old foreign key constraint (though it was incorrect)
        Schema::table('receiving_items', function (Blueprint $table) {
            $table->foreign('receiving_id')->references('id')->on('receivings')->onDelete('cascade');
        });
    }
};
