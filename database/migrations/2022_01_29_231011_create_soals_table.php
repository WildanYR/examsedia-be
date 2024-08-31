<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('nomor');
            $table->string('jenis_soal');
            $table->longText('teks')->nullable();
            $table->longText('opsi_soal')->nullable();
            $table->uuid('kelompok_tes_id');
            $table->foreign('kelompok_tes_id')->references('id')->on('kelompok_tes');
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
        Schema::dropIfExists('soal');
    }
}
