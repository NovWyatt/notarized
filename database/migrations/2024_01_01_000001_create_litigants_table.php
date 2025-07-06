<?php

// Migration 1: Main litigants table
// database/migrations/2024_01_01_000001_create_litigants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLitigantsTable extends Migration
{
    public function up()
    {
        Schema::create('litigants', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable(false); // Không được null
            $table->enum('type', ['individual', 'organization', 'credit_institution']);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Người tạo
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'full_name']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('litigants');
    }
}
