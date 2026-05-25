<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel pivot many-to-many petugas <-> lokasi
        Schema::create('petugas_lokasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petugas_id')->constrained('petugas')->onDelete('cascade');
            $table->foreignId('lokasi_id')->constrained('lokasi')->onDelete('cascade');
            $table->unique(['petugas_id', 'lokasi_id']);
            $table->timestamps();
        });

        // Migrasi data dari kolom JSON id_lokasi ke tabel pivot
        $rows = DB::table('petugas')->whereNotNull('id_lokasi')->get();
        foreach ($rows as $row) {
            $ids = json_decode($row->id_lokasi, true);
            if (is_array($ids)) {
                foreach ($ids as $lokasiId) {
                    DB::table('petugas_lokasi')->insertOrIgnore([
                        'petugas_id' => $row->id,
                        'lokasi_id'  => $lokasiId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Hapus kolom id_lokasi dari tabel petugas
        Schema::table('petugas', function (Blueprint $table) {
            $table->dropColumn('id_lokasi');
        });
    }

    public function down(): void
    {
        // Kembalikan kolom id_lokasi
        Schema::table('petugas', function (Blueprint $table) {
            $table->text('id_lokasi')->nullable();
        });

        // Kembalikan data ke JSON
        $pivots = DB::table('petugas_lokasi')
            ->selectRaw('petugas_id, JSON_GROUP_ARRAY(lokasi_id) as ids')
            ->groupBy('petugas_id')
            ->get();
        foreach ($pivots as $p) {
            DB::table('petugas')->where('id', $p->petugas_id)
                ->update(['id_lokasi' => $p->ids]);
        }

        Schema::dropIfExists('petugas_lokasi');
    }
};
