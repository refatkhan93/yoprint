<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Csvproductdetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_product_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('csv_file_id');
            $table->string('UNIQUE_KEY');
            $table->text('PRODUCT_TITLE');
            $table->text('PRODUCT_DESCRIPTION');
            $table->text('STYLE#');
            $table->text('SANMAR_MAINFRAME_COLOR');
            $table->string('SIZE');
            $table->string('COLOR_NAME');
            $table->double('PIECE_PRICE');
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
        Schema::dropIfExists('csv_product_details');
    }
}
