<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\{Transaksi, Tarif, Lokasi, MetodePembayaran};
use App\Services\QrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ParkController extends Controller
{
    public function dashboard(Request $request)
    {
        $user    = Auth::user();
        $petugas = $user->petugas;

        if (!$petugas) {
            return redirect()->route('login')->withErrors(['username' => 'Data petugas tidak ditemukan.']);
        }

        $semuaLokasi = $petugas->lokasi;

        // Simpan pilihan lokasi ke session, atau baca dari session
        if ($request->has('lokasi_id')) {
            $lid = (int) $request->lokasi_id;
            session(['petugas_lokasi_id' => $lid ?: null]);
        }
        $lokasiAktifId = session('petugas_lokasi_id');

        // Validasi lokasi aktif masih milik petugas ini
        $lokasiAktif = $lokasiAktifId
            ? $semuaLokasi->firstWhere('id', $lokasiAktifId)
            : null;

        $today = Carbon::today();

        $baseQ = fn() => Transaksi::where('id_petugas', $petugas->id)
                            ->whereDate('tgl', $today)
                            ->when($lokasiAktif, fn($q) => $q->where('id_lokasi', $lokasiAktif->id));

        $stats = [
            'transaksi_hari_ini'  => $baseQ()->count(),
            'kendaraan_parkir'    => $baseQ()->where('status', 0)->count(),
            'pendapatan_hari_ini' => (float) $baseQ()->where('status', 1)
                                        ->join('tarif', 'transaksi.id_tarif', '=', 'tarif.id')
                                        ->sum('tarif.tarif'),
        ];

        $aktivParkir = Transaksi::with(['tarif', 'lokasi'])
                        ->where('id_petugas', $petugas->id)
                        ->whereDate('tgl', $today)
                        ->where('status', 0)
                        ->when($lokasiAktif, fn($q) => $q->where('id_lokasi', $lokasiAktif->id))
                        ->latest()->limit(10)->get();

        return view('petugas.dashboard.index', compact(
            'petugas', 'semuaLokasi', 'lokasiAktif', 'stats', 'aktivParkir'
        ));
    }

    public function formTambah()
    {
        $petugas       = Auth::user()->petugas;
        $lokasi        = $petugas->lokasi;
        $tarif         = Tarif::all();
        $lokasiAktifId = session('petugas_lokasi_id');
        return view('petugas.dashboard.tambah', compact('lokasi', 'tarif', 'petugas', 'lokasiAktifId'));
    }

    public function simpanMasuk(Request $request)
    {
        $request->validate([
            'nomor_polisi' => 'required|string|max:15',
            'id_tarif'     => 'required|exists:tarif,id',
            'id_lokasi'    => 'required|exists:lokasi,id',
        ]);

        $petugas      = Auth::user()->petugas;
        $nomorPolisi  = strtoupper(trim($request->nomor_polisi));

        // Cek apakah nomor polisi yang sama masih aktif parkir
        $aktif = Transaksi::where('nomor_polisi', $nomorPolisi)
                    ->where('status', 0)->exists();
        if ($aktif) {
            return back()->withErrors(['nomor_polisi' => 'Kendaraan dengan nomor polisi ini masih parkir.'])
                         ->withInput();
        }

        Transaksi::create([
            'reference_number'     => Transaksi::generateNomorReferensi(),
            'billing_number'       => Transaksi::generateBillingNumber(),
            'id_petugas'           => $petugas->id,
            'id_lokasi'            => $request->id_lokasi,
            'tgl'                  => Carbon::today(),
            'id_tarif'             => $request->id_tarif,
            'nomor_polisi'         => $nomorPolisi,
            'jam_masuk'            => Carbon::now()->format('H:i:s'),
            'status'               => 0,
        ]);

        return redirect()->route('petugas.dashboard')->with('success', 'Kendaraan ' . $nomorPolisi . ' berhasil masuk!');
    }

    public function riwayat(Request $request)
    {
        $petugas = Auth::user()->petugas;

        $query = Transaksi::with(['tarif', 'lokasi', 'metodePembayaran'])
                    ->where('id_petugas', $petugas->id);

        if ($request->filled('tgl')) {
            $query->whereDate('tgl', $request->tgl);
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->filled('nomor_polisi')) {
            $query->where('nomor_polisi', 'like', '%' . strtoupper($request->nomor_polisi) . '%');
        }

        $transaksis = $query->latest()->paginate(15)->withQueryString();

        $totalTrx   = Transaksi::where('id_petugas', $petugas->id)
                        ->when($request->filled('tgl'), fn($q) => $q->whereDate('tgl', $request->tgl))
                        ->count();
        $totalLunas = Transaksi::where('id_petugas', $petugas->id)
                        ->when($request->filled('tgl'), fn($q) => $q->whereDate('tgl', $request->tgl))
                        ->where('status', 1)->count();
        $totalBelum = Transaksi::where('id_petugas', $petugas->id)
                        ->when($request->filled('tgl'), fn($q) => $q->whereDate('tgl', $request->tgl))
                        ->where('status', 0)->count();

        return view('petugas.riwayat.index', compact('transaksis', 'totalTrx', 'totalLunas', 'totalBelum'));
    }

    public function scanCheckout()
    {
        return view('petugas.dashboard.scan');
    }

    public function cariCheckout(Request $request)
    {
        $petugas      = Auth::user()->petugas;
        $nomor_polisi = strtoupper(trim($request->nomor_polisi ?? $request->q ?? ''));

        if ($nomor_polisi === '') {
            return response()->json(['found' => false, 'message' => 'Nomor polisi kosong']);
        }

        $transaksi = Transaksi::with(['tarif', 'lokasi'])
                    ->where('id_petugas', $petugas->id)
                    ->where('status', 0)
                    ->where('nomor_polisi', 'like', '%' . $nomor_polisi . '%')
                    ->latest()->first();

        if (!$transaksi) {
            return response()->json(['found' => false, 'message' => 'Transaksi aktif tidak ditemukan']);
        }

        $jamMasuk = Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_masuk);
        $durasi   = $jamMasuk->diffForHumans(now(), ['parts' => 2, 'short' => true, 'syntax' => Carbon::DIFF_ABSOLUTE]);

        $metode = MetodePembayaran::all();

        return response()->json([
            'found' => true,
            'transaksi' => [
                'id'              => $transaksi->id,
                'nomor_referensi' => $transaksi->reference_number,
                'billing_number'  => $transaksi->billing_number,
                'nomor_polisi'    => $transaksi->nomor_polisi,
                'lokasi'          => $transaksi->lokasi->nama ?? '-',
                'tarif_nama'      => $transaksi->tarif->nama ?? '-',
                'tarif_harga'     => (float) ($transaksi->tarif->tarif ?? 0),
                'jam_masuk'       => substr($transaksi->jam_masuk, 0, 5),
                'durasi'          => $durasi,
                'status'          => $transaksi->status,
                'jam_keluar'      => $transaksi->jam_keluar ? substr($transaksi->jam_keluar, 0, 5) : null,
            ],
            'metode' => $metode->map(fn($m) => ['id' => $m->id, 'nama' => $m->nama])->values(),
        ]);
    }

    public function detailCheckout(Request $request, Transaksi $transaksi)
    {
        $transaksi->load(['tarif', 'lokasi', 'metodePembayaran']);

        if ($request->wantsJson()) {
            $jamMasuk = Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_masuk);
            $durasi   = $transaksi->jam_keluar
                ? $jamMasuk->diffForHumans(
                    Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_keluar),
                    ['parts' => 2, 'short' => true, 'syntax' => Carbon::DIFF_ABSOLUTE]
                  )
                : $jamMasuk->diffForHumans(now(), ['parts' => 2, 'short' => true, 'syntax' => Carbon::DIFF_ABSOLUTE]);

            return response()->json([
                'transaksi' => [
                    'id'              => $transaksi->id,
                    'nomor_referensi' => $transaksi->reference_number,
                    'nomor_polisi'    => $transaksi->nomor_polisi,
                    'lokasi'          => $transaksi->lokasi->nama ?? '-',
                    'tarif_nama'      => $transaksi->tarif->nama ?? '-',
                    'tarif_harga'     => (float) ($transaksi->tarif->tarif ?? 0),
                    'jam_masuk'       => substr($transaksi->jam_masuk, 0, 5),
                    'jam_keluar'      => $transaksi->jam_keluar ? substr($transaksi->jam_keluar, 0, 5) : null,
                    'durasi'          => $durasi,
                    'status'          => $transaksi->status,
                    'metode_nama'     => $transaksi->metodePembayaran->nama ?? null,
                ],
            ]);
        }

        $metode = MetodePembayaran::all();
        return view('petugas.dashboard.checkout', compact('transaksi', 'metode'));
    }

    /**
     * Proses checkout untuk metode CASH (langsung lunas).
     * Untuk QRIS, gunakan generateQris() lalu tunggu callback bank.
     */
    public function prosesCheckout(Request $request, Transaksi $transaksi)
    {
        $request->validate(['id_metode_pembayaran' => 'required|exists:metode_pembayaran,id']);

        if ($transaksi->status == 1) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Transaksi sudah lunas']);
            }
            return back()->withErrors(['msg' => 'Transaksi sudah lunas']);
        }

        $jamMasuk  = Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_masuk);
        $jamKeluar = Carbon::now();
        $durasi    = $jamMasuk->diffForHumans($jamKeluar, ['parts' => 2, 'short' => true, 'syntax' => Carbon::DIFF_ABSOLUTE]);

        $transaksi->update([
            'jam_keluar'           => $jamKeluar->format('H:i:s'),
            'id_metode_pembayaran' => $request->id_metode_pembayaran,
            'amount'               => (string) ($transaksi->tarif->tarif ?? 0),
            'status'               => 1,
        ]);

        $metode = MetodePembayaran::find($request->id_metode_pembayaran);

        if ($request->wantsJson()) {
            return response()->json([
                'success'          => true,
                'message'          => 'Checkout berhasil',
                'nomor_referensi'  => $transaksi->reference_number,
                'nomor_polisi'     => $transaksi->nomor_polisi,
                'durasi'           => $durasi,
                'metode_nama'      => $metode->nama ?? '-',
                'total'            => (float) ($transaksi->tarif->tarif ?? 0),
                'redirect'         => route('petugas.checkout-sukses', $transaksi->id),
            ]);
        }

        return redirect()->route('petugas.checkout-sukses', $transaksi->id);
    }

    /**
     * Generate QRIS via Bank Nagari untuk transaksi tertentu.
     * Set jam_keluar + metode (status tetap 0 sampai dibayar).
     */
    public function generateQris(Request $request, Transaksi $transaksi, QrisService $qris)
    {
        if ($transaksi->status == 1) {
            return response()->json(['success' => false, 'message' => 'Transaksi sudah lunas'], 422);
        }

        $jamMasuk  = Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_masuk);
        $jamKeluar = Carbon::now();
        $amount    = (int) ($transaksi->tarif->tarif ?? 0);

        // Set metode QRIS, jam_keluar (sementara), outlet/pjsp — status biarkan 0.
        $metodeQris = MetodePembayaran::whereRaw('LOWER(nama) = ?', ['qris'])->first();

        $transaksi->update([
            'jam_keluar'           => $jamKeluar->format('H:i:s'),
            'id_metode_pembayaran' => $metodeQris->id ?? null,
            'amount'               => (string) $amount,
            'outlet_id'            => $qris->getOutletId(),
            'pjsp'                 => $qris->getPjsp(),
        ]);

        $result = $qris->generate($amount, $transaksi->billing_number);

        if (!$result['ok']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate QRIS: ' . ($result['error'] ?? 'unknown'),
                'raw'     => $result['raw'],
            ], 502);
        }

        return response()->json([
            'success'        => true,
            'qr_string'      => $result['qr_string'],
            'qr_type'        => $result['qr_type'] ?? 'qr_string', // ← base64_png atau qr_string
            'billing_number' => $transaksi->billing_number,
            'amount'         => $amount,
            'expires_at'     => Carbon::now()->addMinutes(5)->toIso8601String(),
            'status_url'     => route('petugas.checkout-status', $transaksi->id),
            'sukses_url'     => route('petugas.checkout-sukses', $transaksi->id),
        ]);
    }

    /**
     * Polling endpoint — frontend cek status setiap 3-5 detik.
     */
    public function checkStatus(Transaksi $transaksi)
    {
        $transaksi->load('metodePembayaran');

        return response()->json([
            'success'    => true,
            'status'     => (int) $transaksi->status,
            'lunas'      => (int) $transaksi->status === 1,
            'metode'     => $transaksi->metodePembayaran->nama ?? null,
            'amount'     => $transaksi->amount,
            'sukses_url' => route('petugas.checkout-sukses', $transaksi->id),
        ]);
    }

    /**
     * Halaman sukses pembayaran (animasi).
     */
    public function sukses(Transaksi $transaksi)
    {
        $transaksi->load(['tarif', 'lokasi', 'metodePembayaran']);

        if ($transaksi->status != 1) {
            return redirect()->route('petugas.dashboard')
                ->withErrors(['msg' => 'Transaksi belum lunas.']);
        }

        $jamMasuk  = Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_masuk);
        $jamKeluar = $transaksi->jam_keluar
            ? Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_keluar)
            : Carbon::now();
        $durasi    = $jamMasuk->diffForHumans($jamKeluar, ['parts' => 2, 'short' => true, 'syntax' => Carbon::DIFF_ABSOLUTE]);

        return view('petugas.dashboard.sukses', compact('transaksi', 'durasi'));
    }

    /**
     * Proxy OCR ke OCR.space. Menerima image base64 dari client,
     * mengembalikan plat nomor terdeteksi + raw text.
     * Tujuan: sembunyikan API key & jamin CORS.
     */
    public function scanPlate(Request $request)
    {
        $request->validate([
            'image' => 'required|string', // data URL base64
        ]);

        $img = $request->input('image');
        // Pastikan format data URL valid
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/i', $img)) {
            return response()->json(['success' => false, 'message' => 'Format gambar tidak valid'], 422);
        }

        $apiKey   = config('services.ocr_space.key');
        $endpoint = config('services.ocr_space.endpoint');
        $lang     = config('services.ocr_space.language', 'eng');
        $engine   = (int) config('services.ocr_space.engine', 2);

        try {
            $resp = Http::timeout(20)
                ->asForm()
                ->withHeaders(['apikey' => $apiKey])
                ->post($endpoint, [
                    'language'           => $lang,
                    'OCREngine'          => $engine,
                    'scale'              => 'true',
                    'isTable'            => 'false',
                    'isOverlayRequired'  => 'false',
                    'detectOrientation'  => 'true',
                    'base64Image'        => $img,
                ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi server OCR: ' . $e->getMessage(),
            ], 502);
        }

        if (!$resp->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'OCR server merespons error (' . $resp->status() . ')',
            ], 502);
        }

        $data = $resp->json();

        if (($data['IsErroredOnProcessing'] ?? false) === true) {
            $err = $data['ErrorMessage'] ?? ['OCR error'];
            return response()->json([
                'success' => false,
                'message' => is_array($err) ? implode('; ', $err) : (string) $err,
            ], 502);
        }

        $rawText = '';
        foreach ($data['ParsedResults'] ?? [] as $r) {
            $rawText .= ($r['ParsedText'] ?? '') . "\n";
        }

        $plate = $this->extractPlateFromText($rawText);

        return response()->json([
            'success'  => true,
            'plate'    => $plate,
            'raw'      => trim($rawText),
        ]);
    }

    /**
     * Cari pola plat Indonesia di dalam teks OCR.
     * Format umum: [1-2 huruf][spasi][1-4 digit][spasi][1-3 huruf].
     */
    protected function extractPlateFromText(string $text): ?string
    {
        $cleaned = strtoupper(preg_replace('/[^A-Z0-9 \n]/i', ' ', $text));

        // Cari pola yang paling cocok di seluruh baris (mungkin berantakan)
        $candidates = [];
        foreach (preg_split('/\s+/', $cleaned) as $token) {
            if ($token === '') continue;
            $candidates[] = $token;
        }
        // Gabungkan juga seluruh teks tanpa spasi untuk pencarian regex luas
        $flat = preg_replace('/\s+/', '', $cleaned);

        // Coba regex full plate dulu di teks dengan spasi
        if (preg_match_all('/\b([A-Z]{1,2})\s?(\d{1,4})\s?([A-Z]{1,3})\b/', $cleaned, $matches, PREG_SET_ORDER)) {
            // Pilih yang panjang totalnya paling masuk akal (5-10 char)
            $best = null;
            foreach ($matches as $m) {
                $plate = "{$m[1]} {$m[2]} {$m[3]}";
                $len   = strlen(str_replace(' ', '', $plate));
                if ($len >= 5 && $len <= 10) {
                    if (!$best || strlen(str_replace(' ', '', $best)) < $len) {
                        $best = $plate;
                    }
                }
            }
            if ($best) return $best;
        }

        // Fallback: regex di string flat
        if (preg_match('/([A-Z]{1,2})(\d{1,4})([A-Z]{1,3})/', $flat, $m)) {
            return "{$m[1]} {$m[2]} {$m[3]}";
        }

        return null;
    }

    public function profile()
    {
        $user          = Auth::user();
        $petugas       = $user->petugas;
        $lokasiPetugas = $petugas ? $petugas->lokasi : collect();
        $today         = Carbon::today();

        $statHariIni = $petugas ? [
            'total'  => Transaksi::where('id_petugas', $petugas->id)->whereDate('tgl', $today)->count(),
            'lunas'  => Transaksi::where('id_petugas', $petugas->id)->whereDate('tgl', $today)->where('status', 1)->count(),
            'parkir' => Transaksi::where('id_petugas', $petugas->id)->whereDate('tgl', $today)->where('status', 0)->count(),
        ] : ['total' => 0, 'lunas' => 0, 'parkir' => 0];

        $statTotal = $petugas ? [
            'total'     => Transaksi::where('id_petugas', $petugas->id)->count(),
            'pendapatan'=> (float) Transaksi::where('id_petugas', $petugas->id)
                                ->where('status', 1)
                                ->join('tarif', 'transaksi.id_tarif', '=', 'tarif.id')
                                ->sum('tarif.tarif'),
        ] : ['total' => 0, 'pendapatan' => 0];

        return view('petugas.profile.index', compact('petugas', 'lokasiPetugas', 'statHariIni', 'statTotal'));
    }
}
