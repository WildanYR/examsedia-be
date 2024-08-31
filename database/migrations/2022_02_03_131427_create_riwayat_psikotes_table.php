<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatPsikotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riwayat_psikotes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();
            $table->longText('jawaban')->nullable();
            $table->uuid('user_id');
            $table->uuid('sesi_id');
            $table->uuid('alat_tes_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('sesi_id')->references('id')->on('sesi');
            $table->foreign('alat_tes_id')->references('id')->on('alat_tes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riwayat_psikotes');
    }
}
