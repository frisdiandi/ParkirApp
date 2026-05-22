<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransaksiStatusController extends Controller
{
    /**
     * POST /api/transaksi/update-status
     *
     * Payload:
     * {
     *   "outlet_id":        "007210024",
     *   "billing_number":   "12345",
     *   "amount":           "1000",
     *   "reference_number": "2050901570",
     *   "pjsp":             "NGR",
     *   "customer_name":    "AFRIZON INDRA"
     * }
     */
    public function updateStatus(Request $request): JsonResponse
    {
        // ── 1. Validasi input ────────────────────────────────────────────────
        $validator = Validator::make($request->all(), [
            'outlet_id'        => 'required|string|max:50',
            'billing_number'   => 'required|string|max:100',
            'amount'           => 'required|string|max:50',
            'reference_number' => 'required|string|max:100',
            'pjsp'             => 'required|string|max:50',
            'customer_name'    => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // ── 2. Cari transaksi berdasarkan billing_number ──────────────────────
        $transaksi = Transaksi::where('billing_number', $request->billing_number)->first();

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi dengan billing_number ' . $request->billing_number . ' tidak ditemukan.',
            ], 404);
        }

        // ── 3. Cek duplikat unique: outlet_id + reference_number + customer_name ─
        //      (boleh sama dengan dirinya sendiri, cegah konflik dengan baris lain)
        $duplikat = Transaksi::where('outlet_id', $request->outlet_id)
            ->where('reference_number', $request->reference_number)
            ->where('customer_name', $request->customer_name)
            ->where('id', '!=', $transaksi->id)
            ->exists();

        if ($duplikat) {
            return response()->json([
                'success' => false,
                'message' => 'Kombinasi outlet_id, reference_number, dan customer_name sudah digunakan transaksi lain.',
            ], 409);
        }

        // ── 4. Update dalam transaksi DB ──────────────────────────────────────
        try {
            DB::beginTransaction();

            $transaksi->update([
                'outlet_id'        => $request->outlet_id,
                'amount'           => $request->amount,
                'reference_number' => $request->reference_number,
                'pjsp'             => $request->pjsp,
                'customer_name'    => $request->customer_name,
                'status'           => 1, // 1 = Lunas
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status transaksi berhasil diperbarui.',
                'data'    => [
                    'id'               => $transaksi->id,
                    'billing_number'   => $transaksi->billing_number,
                    'outlet_id'        => $transaksi->outlet_id,
                    'amount'           => $transaksi->amount,
                    'reference_number' => $transaksi->reference_number,
                    'pjsp'             => $transaksi->pjsp,
                    'customer_name'    => $transaksi->customer_name,
                    'status'           => $transaksi->status,
                    'status_label'     => $transaksi->status_label,
                ],
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}