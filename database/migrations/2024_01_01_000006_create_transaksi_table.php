<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('outlet_id')->nullable();
            $table->string('billing_number')->uniqid();
            $table->string('amount')->nullable();
            $table->string('pjsp')->nullable();
            $table->string('reference_number')->uniqid();
            $table->string('customer_name')->nullable();
            $table->foreignId('id_petugas')->constrained('petugas')->onDelete('cascade');
            $table->foreignId('id_lokasi')->constrained('lokasi')->onDelete('cascade');
            $table->date('tgl');
            $table->foreignId('id_tarif')->constrained('tarif')->onDelete('cascade');
            $table->string('nomor_polisi');
            $table->time('jam_masuk');
            $table->time('jam_keluar')->nullable();
            $table->foreignId('id_metode_pembayaran')->nullable()->constrained('metode_pembayaran')->onDelete('set null');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            // Unique composite
            $table->unique(['outlet_id', 'reference_number', 'customer_name'], 'uniq_outlet_ref_customer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};