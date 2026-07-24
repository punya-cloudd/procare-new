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
        Schema::create('bouchard', function (Blueprint $table) {

            $table->id();

            // RELASI
            $table->unsignedBigInteger('peserta_id')
                ->comment('Relasi ke tabel peserta');

            $table->unsignedBigInteger('petugas_id')
                ->nullable()
                ->comment('Relasi ke tabel petugas');

            // TANGGAL MONITORING
            $table->date('tanggal');

            // BERAT BADAN
            $table->decimal('berat_badan', 5, 2)
                ->nullable()
                ->comment('Berat badan (Kg)');

            // TOTAL PENGELUARAN ENERGI HARIAN
            $table->decimal('total_kalori', 10, 2)
                ->default(0)
                ->comment('Total pengeluaran energi (kkal/hari)');

            // KATEGORI AKTIVITAS
            $table->string('kategori')
                ->nullable()
                ->comment('Ringan, Sedang, Berat');

            // CATATAN
            $table->text('catatan')->nullable();

            // AUDIT
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // FOREIGN KEY
            $table->foreign('peserta_id')
                ->references('id')
                ->on('peserta')
                ->onDelete('cascade');

            $table->foreign('petugas_id')
                ->references('id')
                ->on('petugas')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // INDEX
            $table->index('peserta_id');
            $table->index('tanggal');

            // Satu peserta hanya boleh satu monitoring per hari
            $table->unique([
                'peserta_id',
                'tanggal'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouchard');
    }
};
