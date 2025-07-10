<?php

// 2024_01_01_0000013_create_certificates_table.php
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
            $table->enum('certificate_type', [
                'land_use_certificate',
                'apartment_ownership_certificate',
                'land_house_ownership_certificate',
                'house_ownership_certificate',
                'land_use_right_certificate',
                'bl735265'
            ]);
            $table->string('issue_number', 50)->nullable();
            $table->string('book_number', 50)->nullable();
            $table->date('issue_date')->nullable();
            $table->timestamps();

            $table->index('asset_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};
