<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKelompokTesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kelompok_tes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("nama");
            $table->longText("petunjuk")->nullable();
            $table->integer("waktu")->unsigned()->default(0);
            $table->uuid("alat_tes_id");
            $table->foreign("alat_tes_id")->references("id")->on("alat_tes");
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
        Schema::dropIfExists('kelompok_tes');
    }
}
