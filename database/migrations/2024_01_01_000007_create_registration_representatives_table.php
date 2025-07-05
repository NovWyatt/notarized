<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration 7: Registration representatives
// database/migrations/2024_01_01_000007_create_registration_representatives_table.php

class CreateRegistrationRepresentativesTable extends Migration
{
    public function up()
    {
        Schema::create('registration_representatives', function (Blueprint $table) {
            $table->id();
            // Tạo thủ công thay vì dùng morphs() để tránh tên index tự động quá dài
            $table->string('representable_type');
            $table->unsignedBigInteger('representable_id');
            $table->foreignId('representative_id')->constrained('litigants')->onDelete('cascade');
            $table->string('position')->nullable();
            $table->text('legal_basis')->nullable();
            $table->timestamps();

            // Đặt tên index ngắn
            $table->index(['representable_type', 'representable_id'], 'reg_reps_morph_idx');
            $table->index('representative_id', 'reg_reps_rep_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('registration_representatives');
    }
}
