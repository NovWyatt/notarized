<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 6: Organization additional information
// database/migrations/2024_01_01_000006_create_organization_additional_info_table.php

class CreateOrganizationAdditionalInfoTable extends Migration
{
    public function up()
    {
        Schema::create('organization_additional_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('former_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->integer('change_registration_number')->nullable();
            $table->date('change_registration_date')->nullable();
            $table->timestamps();

            $table->index('organization_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('organization_additional_info');
    }
}
