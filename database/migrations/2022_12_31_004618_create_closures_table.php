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
        Schema::create('closures', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('month');
            $table->string('tot_qty')->default(0);
            $table->string('avl_qty')->default(0);
            $table->string('amt_sold')->default(0);
            $table->string('exp_amt')->nullable();
            $table->string('profits')->default(0);
            $table->string('q1')->default(0);
            $table->string('q2')->default(0);
            $table->string('q3')->default(0);
            $table->string('q4')->default(0);
            $table->string('q5')->default(0);
            $table->string('q6')->default(0);
            $table->string('q7')->default(0);
            $table->string('status')->default('no');
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
        Schema::dropIfExists('closures');
    }
};
