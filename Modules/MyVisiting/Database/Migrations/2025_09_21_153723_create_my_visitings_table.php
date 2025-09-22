<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyVisitingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_visitings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("dealer_id");
            $table->foreign('dealer_id')->references('id')->on('shops')->onDelete('cascade');
            $table->unsignedBigInteger('ref_id');
            $table->foreign('ref_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('photo_of_shop');
            $table->time('time');
            $table->json('location');
            $table->date('date');
            $table->time('checkout_time')->nullable();
            $table->date('checkout_date')->nullable();
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
        Schema::dropIfExists('my_visitings');
    }
}
