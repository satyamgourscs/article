<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(){Schema::create("bank_details",function(Blueprint $table){$table->id();$table->unsignedBigInteger("buyer_id")->nullable();$table->string("account_holder_name")->nullable();$table->string("account_number")->nullable();$table->string("bank_name")->nullable();$table->string("ifsc")->nullable();$table->string("upi_id")->nullable();$table->timestamps();});}
public function down(){Schema::dropIfExists("bank_details");}
};
