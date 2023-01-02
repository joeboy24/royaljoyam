<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id');
            $table->string('user_bv');
            $table->string('order_no')->unique();
            $table->string('qty');
            $table->string('tot');
            $table->string('paid_debt')->default('0');
            $table->string('pay_mode');
            $table->string('buy_name');
            $table->string('buy_contact');
            $table->string('del_status');
            $table->string('discount')->default('0');
            $table->string('payment')->default(0);
            $table->string('change')->default(0);
            $table->string('paid');
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
        Schema::dropIfExists('sales');
    }
}
