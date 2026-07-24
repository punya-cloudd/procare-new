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
        Schema::create('master_makanan', function (Blueprint $table) {

            $table->id();

            // Kode makanan (opsional)
            $table->string('kode', 30)->nullable()->unique();

            // Nama makanan
            $table->string('nama');

            // Kategori makanan (diubah dari enum ke string agar lebih fleksibel)
            $table->string('kategori', 100)->default('Lainnya');

            // Satuan penyajian
            $table->string('satuan', 30);

            // Berat untuk 1 satuan
            $table->decimal('gram', 8, 2);

            // Kalori per 1 satuan
            $table->decimal('kalori', 8, 2);

            // Keterangan
            $table->text('keterangan')->nullable();

            // Status
            $table->boolean('aktif')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_makanan');
    }
};
