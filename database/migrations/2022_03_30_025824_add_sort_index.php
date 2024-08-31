<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            $table->integer('sort_index')->default(0);
        });
        Schema::table('kelompok_tes', function (Blueprint $table) {
            $table->integer('sort_index')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alat_tes', function (Blueprint $table) {
            $table->dropColumn('sort_index');
        });
        Schema::table('kelompok_tes', function (Blueprint $table) {
            $table->dropColumn('sort_index');
        });
    }
}
