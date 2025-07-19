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
        Schema::table('certificates', function (Blueprint $table) {
            $table->unsignedBigInteger('issuing_authority_id')->nullable()->after('certificate_type_id');
            $table->foreign('issuing_authority_id')->references('id')->on('issuing_authorities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['issuing_authority_id']);
            $table->dropColumn('issuing_authority_id');
        });
    }
};
