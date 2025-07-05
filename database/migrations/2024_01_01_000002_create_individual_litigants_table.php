<?php

// Migration 2: Individual information
// database/migrations/2024_01_01_000002_create_individual_litigants_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndividualLitigantsTable extends Migration
{
    public function up()
    {
        Schema::create('individual_litigants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('litigant_id')->constrained('litigants')->onDelete('cascade');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('nationality')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['alive', 'deceased', 'civil_incapacitated'])->default('alive');
            $table->enum('marital_status', ['single', 'divorced', 'married'])->default('single');
            $table->string('marriage_certificate_number')->nullable();
            $table->date('marriage_certificate_date')->nullable();
            $table->string('marriage_certificate_issued_by')->nullable();
            $table->text('marriage_notes')->nullable();
            $table->timestamps();

            $table->index('litigant_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('individual_litigants');
    }
}
