<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('klub_sepakbola', function (Blueprint $table) {
            $table->char('id_klub', 10)->primary();
            $table->string('nama_klub', 20);
            $table->date('tgl_berdiri');
            $table->integer('kondisi_klub');
            $table->string('kota_klub', 30);
            $table->char('peringkat', 10);
            $table->integer('harga_klub');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klub_sepakbola');
    }
};
