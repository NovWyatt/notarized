<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 9: Credit institution additional information
// database/migrations/2024_01_01_000009_create_credit_institution_additional_info_table.php

class CreateCreditInstitutionAdditionalInfoTable extends Migration
{
    public function up()
    {
        Schema::create('credit_institution_additional_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_institution_id')->constrained('credit_institutions')->onDelete('cascade');
            $table->string('former_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->integer('change_registration_number')->nullable();
            $table->date('change_registration_date')->nullable();
            $table->timestamps();

            $table->index('credit_institution_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_institution_additional_info');
    }
}
