<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->increments('id_p');
            $table->string('comment')->nullable();
            $table->string('softline_SKU');
            $table->string('vendor_SKU')->nullable();
            $table->string('product_description');
            $table->string('version')->nullable();
            $table->enum('language', [
                    'All Languages',
                    'English',
                    'Russian',
                    'English International',
                    'Single Language',
                    'Non-specific'
                ]);
            $table->string('full_upgrade');
            $table->enum('box_lic', [
                    'Box',
                    'Lic'
                ]);
            $table->string('ae_com');
            $table->enum('media', [
                    'ESD',
                    'DVD',
                    'Non-specific',
                    '(пусто);DVD',
                    'USB Flash Drive',
                    'CD',
                    'Paper'
                ]);
            $table->enum('os', [
                    'Windows',
                    '32-bit Win and 64-bit Win',
                    'Non-specific',
                    '64-Bit Win',
                    'Win',
                    'Mac',
                    '(пусто)'
                ]);
            $table->enum('license_level', [
                    'No Level',
                    'Level A',
                    'Level B',
                    'Level C',
                    ''
                ])->nullable();
            $table->string('point')->nullable();
            $table->enum('license_comment', [
                    'Applications',
                    'Servers',
                    'Non-specific',
                    'Systems',
                    ''
                ])->nullable();
            $table->double('retail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
