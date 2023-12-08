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
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign('contacts_label_id_foreign');
            $table->dropColumn('label_id');
//            DB::statement('ALTER TABLE contacts DROP FOREIGN KEY label_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreign('label_id')->references('id')->on('labels');
            $table->unsignedBigInteger('label_id');
        });
    }
};
