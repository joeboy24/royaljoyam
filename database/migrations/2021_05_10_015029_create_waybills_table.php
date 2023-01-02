<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaybillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waybills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id');
            $table->string('stock_no');
            $table->string('comp_name');
            $table->string('comp_add');
            $table->string('comp_contact');
            $table->string('drv_name');
            $table->string('drv_contact');
            $table->string('vno');
            $table->string('bill_no')->unique();
            $table->string('weight')->nullable();
            $table->string('nop')->nullable();
            $table->string('tot_qty')->nullable();
            $table->string('del_date');
            $table->string('status');
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
        Schema::dropIfExists('waybills');
    }
}
