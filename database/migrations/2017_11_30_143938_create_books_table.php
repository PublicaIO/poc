<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('title');
            $table->string('url')->unique()->nullable();
            $table->text('short_description')->nullable();
            $table->text('promotion_text')->nullable();
            $table->float('goal', 26, 18);
            $table->float('price_for_crowdsale', 26, 18);
            $table->float('soft_cap', 26, 18);
            $table->text('soft_cap_description')->nullable();
            $table->float('duration', 26, 18);
            $table->float('price_after_crowdsale', 26, 18);
            $table->float('aftersale_keys_amount', 26, 18);
            $table->integer('status')->default(0);
            $table->string('contract_address')->nullable();
            $table->string('contract_transaction')->nullable();
            $table->string('aws_path')->nullable();
            $table->string('cover_art')->nullable();
            $table->string('md5')->nullable();
            $table->dateTime('crowdsale_start_date')->nullable();
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
        Schema::dropIfExists('books');
    }
}
