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
        Schema::create('tailor_measurements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // / Menghubungkan ke tabel users
            $table->uuid('user_id')->constrained('users')->onDelete('cascade');
            
            // Ukuran Baju (Atasan)
            $table->integer('lebar_baju')->comment('dalam cm');
            $table->integer('lengan')->comment('dalam cm');
            $table->integer('panjang_baju')->comment('dalam cm');
            $table->integer('lebar_bahu')->comment('dalam cm');
            
            // Ukuran Celana (Bawahan)
            $table->integer('lingkar_pinggang')->comment('dalam cm');
            $table->integer('panjang_bawahan')->comment('dalam cm');

            // Keterangan
            $table->text('keterangan')->nullable();
                      
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tailor_measurements');
    }
};
