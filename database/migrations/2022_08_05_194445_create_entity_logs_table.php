<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntityLogsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 32);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('attribute', 32);
            $table->string('old_value', 2048)->nullable();
            $table->string('new_value', 2048)->nullable();
            $table->unsignedBigInteger('upload_seq')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('barcode')->references('barcode')->on('entities')->onDelete('NO ACTION')->onUpdate('NO ACTION');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('NO ACTION')->onUpdate('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('entity_logs');
    }
}
