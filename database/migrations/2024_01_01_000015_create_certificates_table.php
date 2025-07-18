<?php

// 2024_01_01_0000015_create_certificates_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('certificate_type_id')->constrained()->onDelete('restrict');
            $table->string('issue_number', 50)->nullable();
            $table->string('book_number', 50)->nullable();
            $table->date('issue_date')->nullable();
            $table->timestamps();

            $table->index('asset_id');
            $table->index('certificate_type_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};
