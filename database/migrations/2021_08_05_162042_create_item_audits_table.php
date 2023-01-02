<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_audits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('item_no', 30);
            $table->string('user_id', 30);
            $table->string('itemimage_id', 30)->nullable();
            $table->string('name', 50);
            $table->string('desc', 30)->nullable();
            $table->string('cat', 30)->nullable();
            $table->string('brand', 30)->nullable();
            $table->string('barcode', 30)->nullable();
            $table->string('qty', 30);
            // $table->string('cost_price', 30)->nullable();
            $table->string('price', 30)->nullable();
            $table->string('cost_price', 30)->nullable();
            $table->string('profits', 30)->default(0);
            $table->string('img', 30)->nullable();
            $table->string('thumb_img', 30)->nullable();
            $table->string('bm', 30)->default(0);
            $table->string('b1', 30)->default(0);
            $table->string('b2', 30)->default(0);
            $table->string('b3', 30)->default(0);
            $table->string('b4', 30)->nullable();
            $table->string('b5', 30)->nullable();
            $table->string('b6', 30)->nullable();
            $table->string('b7', 30)->nullable();
            $table->string('qm', 30)->default(0);
            $table->string('q1', 30)->default(0);
            $table->string('q2', 30)->default(0);
            $table->string('q3', 30)->default(0);
            $table->string('q4', 30)->nullable();
            $table->string('q5', 30)->nullable();
            $table->string('q6', 30)->nullable();
            $table->string('q7', 30)->nullable();
            $table->string('publish')->default('yes', 30);
            $table->string('del')->default('no', 30);
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
        Schema::dropIfExists('item_audits', 30);
    }
}
