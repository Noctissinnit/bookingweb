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
        Schema::create('user_jabatans', function (Blueprint $table) {
            $table->id('id_user_jabatan'); // Kolom id_user_jabatan sebagai primary key
            $table->unsignedBigInteger('id_user'); // Kolom id_user
            $table->unsignedBigInteger('id_jabatan'); // Kolom id_jabatan
            $table->unsignedBigInteger('id_department'); // Kolom id_department

            // Menambahkan foreign key
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatans')->onDelete('cascade');
            $table->foreign('id_department')->references('id_department')->on('departments')->onDelete('cascade');

            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_jabatans');
    }
};
