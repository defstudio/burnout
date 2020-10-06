<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBurnoutTable extends Migration
{
    public function up()
    {
        Schema::create('burnout_entries', function (Blueprint $table) {
            $table->id('id');
            $table->string('message');
            $table->string('file');
            $table->integer('line');
            $table->text('trace');
            $table->json('report');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('burnout_entries');
    }
}
