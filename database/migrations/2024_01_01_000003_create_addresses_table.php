<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// Migration 3: Addresses table (shared for all types)
// database/migrations/2024_01_01_000003_create_addresses_table.php

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable'); // litigant_id + addressable_type
            $table->enum('address_type', ['permanent', 'temporary', 'headquarters']);
            $table->string('street_address')->nullable();
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->timestamps();

            $table->index(['addressable_type', 'addressable_id', 'address_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
