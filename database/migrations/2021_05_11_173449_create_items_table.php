<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('item_no');
            $table->string('user_id');
            $table->string('itemimage_id')->nullable();
            $table->string('name');
            $table->string('desc')->nullable();
            $table->string('cat')->nullable();
            $table->string('brand')->nullable();
            $table->string('barcode')->nullable();
            $table->string('qty');
            // $table->string('cost_price')->nullable();
            $table->string('price')->nullable();
            $table->string('cost_price')->nullable();
            $table->string('profits')->default(0);
            
            $table->string('img')->nullable();
            $table->string('thumb_img')->nullable();
            $table->string('bm')->default(0);
            $table->string('b1')->default(0);
            $table->string('b2')->default(0);
            $table->string('b3')->default(0);
            $table->string('b4')->nullable();
            $table->string('b5')->nullable();
            $table->string('b6')->nullable();
            $table->string('b7')->nullable();
            $table->string('qm')->default(0);
            $table->string('q1')->default(0);
            $table->string('q2')->default(0);
            $table->string('q3')->default(0);
            $table->string('q4')->nullable();
            $table->string('q5')->nullable();
            $table->string('q6')->nullable();
            $table->string('q7')->nullable();
            $table->string('publish')->default('yes');
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
        Schema::dropIfExists('items');
    }
}
