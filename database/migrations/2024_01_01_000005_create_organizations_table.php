<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 5: Organizations table
// database/migrations/2024_01_01_000005_create_organizations_table.php

class CreateOrganizationsTable extends Migration
{
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('litigant_id')->constrained('litigants')->onDelete('cascade');
            $table->string('business_type')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('organization_type', ['headquarters', 'branch', 'transaction_office']);
            $table->string('license_type')->nullable();
            $table->string('license_number')->nullable();
            $table->date('business_registration_date')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->foreignId('representative_id')->nullable()->constrained('litigants')->onDelete('set null');
            $table->string('representative_position')->nullable();
            $table->timestamps();

            $table->index('litigant_id');
            $table->index('representative_id');
        });
    }
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
