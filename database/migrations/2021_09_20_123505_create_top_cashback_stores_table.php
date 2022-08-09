<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopCashbackStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_cashback_stores', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->string('logo');
            $table->string('cashback');
            $table->string('tandc')->nullable();
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
        Schema::dropIfExists('top_cashback_stores');
    }
}
