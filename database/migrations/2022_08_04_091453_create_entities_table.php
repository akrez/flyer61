<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->string('barcode', 32)->primary();
            $table->string('entity_type', 32);
            $table->string('title', 512)->nullable();
            $table->string('qty', 32)->nullable();
            $table->string('place', 32)->nullable();
            $table->string('description', 2048)->nullable();
            $table->unsignedBigInteger('upload_seq')->nullable();
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
        Schema::dropIfExists('entities');
    }
}
