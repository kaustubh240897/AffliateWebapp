<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id('id');
            $table->string('title');
            $table->string('merchant');
            $table->string('categories');
            $table->string('description')->nullable();
            $table->string('terms')->nullable();
            $table->string('couponCode')->nullable();
            $table->string('URL');
            $table->string('status');
            $table->string('startDate');
            $table->string('endDate');
            $table->string('offerAddedAt');
            $table->string('imageURL');
            $table->string('campaignID');
            $table->string('campaignName');
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
        Schema::dropIfExists('coupons');
    }
}
