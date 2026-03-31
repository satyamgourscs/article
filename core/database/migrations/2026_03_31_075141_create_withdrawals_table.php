<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(){Schema::create("withdrawals",function(Blueprint $table){$table->id();$table->unsignedBigInteger("buyer_id");$table->decimal("amount",10,2);$table->string("status")->default("pending");$table->string("utr")->nullable();$table->timestamps();});}
public function down(){Schema::dropIfExists("withdrawals");}
};
