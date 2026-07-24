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
        Schema::create('bouchard_detail', function (Blueprint $table) {

            $table->id();

            // RELASI
            $table->unsignedBigInteger('bouchard_id')
                ->comment('Relasi ke tabel bouchard');

            // JAM
            $table->tinyInteger('jam')
                ->comment('Jam 00 - 23');

            // INTERVAL 15 MENIT

            $table->string('m00', 10)
                ->nullable()
                ->comment('00 - 15 menit');

            $table->string('m15', 10)
                ->nullable()
                ->comment('15 - 30 menit');

            $table->string('m30', 10)
                ->nullable()
                ->comment('30 - 45 menit');

            $table->string('m45', 10)
                ->nullable()
                ->comment('45 - 60 menit');

            $table->timestamps();

            // FOREIGN KEY
            $table->foreign('bouchard_id')
                ->references('id')
                ->on('bouchard')
                ->onDelete('cascade');

            // INDEX
            $table->index('bouchard_id');
            $table->index([
                'bouchard_id',
                'jam'
            ]);

            // Satu jam hanya boleh satu record
            $table->unique([
                'bouchard_id',
                'jam'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bouchard_detail');
    }
};
