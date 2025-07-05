<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 4: Marriage information
// database/migrations/2024_01_01_000004_create_marriage_information_table.php

class CreateMarriageInformationTable extends Migration
{
    public function up()
    {
        Schema::create('marriage_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('litigant_id')->constrained('litigants')->onDelete('cascade');
            $table->boolean('same_household')->default(false);
            $table->foreignId('spouse_id')->nullable()->constrained('litigants')->onDelete('set null');
            $table->string('marriage_registration_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->string('issued_by')->nullable();
            $table->boolean('is_divorced')->default(false);
            $table->timestamps();

            $table->index('litigant_id');
            $table->index('spouse_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marriage_information');
    }
}
