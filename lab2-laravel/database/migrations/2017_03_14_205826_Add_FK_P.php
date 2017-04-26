<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFKP extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function ($table) {
            $table->integer('id_c')->unsigned();
            $table->foreign('id_c')->references('id')->on('category');
            $table->integer('id_spf')->unsigned();
            $table->foreign('id_spf')->references('id')->on('softline_product_family');
            $table->integer('id_pf')->unsigned();
            $table->foreign('id_pf')->references('id')->on('product_family');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign(['id_c', 'id_spf', 'id_pf']);
    }
}
