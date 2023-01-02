<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wbcontents', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('waybill_id');
            $table->string('item_id');
            $table->string('qty')->default(0);
            $table->string('qty_dist')->default(0);
            $table->string('del')->default('no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wbcontents');
    }
};
