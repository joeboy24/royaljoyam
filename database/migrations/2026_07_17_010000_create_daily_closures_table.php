<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_closures', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('scope_key');
            $table->string('branch_label')->nullable();
            $table->string('close_date');
            $table->string('cash')->default('0');
            $table->string('cheque')->default('0');
            $table->string('momo')->default('0');
            $table->string('debt_sold')->default('0');
            $table->string('collected_debt')->default('0');
            $table->string('expenses')->default('0');
            $table->string('gross_collected')->default('0');
            $table->string('net_total')->default('0');
            $table->string('counted_cash')->nullable();
            $table->string('variance')->nullable();
            $table->string('notes')->nullable();
            $table->string('status')->default('closed');
            $table->string('del')->default('no');
            $table->timestamps();

            $table->unique(['close_date', 'scope_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_closures');
    }
};
