<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndividualProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('individual_products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('merchant');
            $table->string('brand');
            $table->string('url');
            $table->string('originalprice');
            $table->string('discountprice');
            $table->string('image');
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
        Schema::dropIfExists('individual_products');
    }
}
