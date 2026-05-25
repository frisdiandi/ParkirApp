<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QrisService
{
    public function __construct(
        protected ?string $endpoint = null,
        protected ?string $authorization = null,
        protected ?string $secretKey = null,
        protected ?string $requestId = null,
        protected ?string $outletId = null,
        protected ?string $pjsp = null,
        protected bool $verifySsl = false,
    ) {
        $cfg = config('services.bank_nagari');
        $this->endpoint      ??= $cfg['endpoint'];
        $this->authorization ??= $cfg['authorization'];
        $this->secretKey     ??= $cfg['secret_key'];
        $this->requestId     ??= $cfg['request_id'];
        $this->outletId      ??= $cfg['outlet_id'];
        $this->pjsp          ??= $cfg['pjsp'];
        $this->verifySsl       = $cfg['verify'] ?? false;
    }

    public function getOutletId(): string { return $this->outletId; }
    public function getPjsp(): string     { return $this->pjsp; }

    /**
     * Generate QRIS untuk billing tertentu.
     *
     * @return array{ok:bool, qr_string:?string, qr_type:string, raw:mixed, error:?string}
     */
    public function generate(int|string $amount, string $billingNumber): array
    {
        $body = [
            'amount'         => (string) $amount,
            'outlet_id'      => $this->outletId,
            'billing_number' => (string) $billingNumber,
        ];

        $bodyJson  = json_encode($body, JSON_UNESCAPED_SLASHES);
        $timestamp = date('Y-m-d H:i:s');

        $params    = strtoupper(trim(preg_replace('/[^A-Za-z0-9{}:,.\-]/', '', $bodyJson)));
        $signature = base64_encode(hash_hmac('sha256', $params . ':' . $timestamp, $this->secretKey, true));

        try {
            $resp = Http::withOptions([
                    'verify' => false,
                    'curl'   => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => 0,
                    ],
                ])
                ->withHeaders([
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                    'Authorization' => $this->authorization,
                    'Timestamp'     => $timestamp,
                    'Signature'     => $signature,
                    'RequestId'     => $this->requestId,
                ])
                ->timeout(20)
                ->withBody($bodyJson, 'application/json')
                ->post($this->endpoint);
        } catch (\Throwable $e) {
            Log::warning('Bank Nagari QRIS request failed', ['err' => $e->getMessage()]);
            return ['ok' => false, 'qr_string' => null, 'qr_type' => 'qr_string', 'raw' => null, 'error' => $e->getMessage()];
        }

        $raw  = $resp->body();
        $json = $resp->json();

        if (!$resp->successful()) {
            return [
                'ok'        => false,
                'qr_string' => null,
                'qr_type'   => 'qr_string',
                'raw'       => $json ?? $raw,
                'error'     => 'HTTP ' . $resp->status(),
            ];
        }

        // Deteksi apakah response mengandung qrData (base64 PNG) atau qrString (teks)
        $isPng    = $this->hasKey($json, 'qrData');
        $qrType   = $isPng ? 'base64_png' : 'qr_string';
        $qrString = $this->findQrString($json);

        if (!$qrString) {
            return [
                'ok'        => false,
                'qr_string' => null,
                'qr_type'   => $qrType,
                'raw'       => $json ?? $raw,
                'error'     => 'QR string tidak ditemukan di response',
            ];
        }

        return [
            'ok'        => true,
            'qr_string' => $qrString,
            'qr_type'   => $qrType,
            'raw'       => $json,
            'error'     => null,
        ];
    }

    /**
     * Cek apakah key tertentu ada di struktur response (case-sensitive).
     */
    protected function hasKey(mixed $data, string $key): bool
    {
        if (!is_array($data) && !is_object($data)) return false;
        $arr   = json_decode(json_encode($data), true) ?: [];
        $found = false;

        array_walk_recursive($arr, function ($val, $k) use ($key, &$found) {
            if ($k === $key && is_string($val) && strlen($val) > 10) {
                $found = true;
            }
        });

        return $found;
    }

    /**
     * Cari field qr secara case-insensitive di struktur response.
     * Prioritaskan qrData (base64 PNG dari Bank Nagari) terlebih dahulu,
     * lalu fallback ke qrString / qr_string / dll (QR teks biasa).
     */
    protected function findQrString(mixed $data): ?string
    {
        if (!is_array($data) && !is_object($data)) return null;
        $arr = json_decode(json_encode($data), true) ?: [];

        // qrData = base64 PNG (Bank Nagari response)
        // qrString / qr_string / qr = plain QR text
        $candidates = ['qrData', 'qrString', 'qr_string', 'qrstring', 'qr', 'qris', 'qrCode', 'qr_code'];

        $found = null;
        array_walk_recursive($arr, function ($val, $key) use ($candidates, &$found) {
            if ($found) return;
            if (in_array($key, $candidates, true) && is_string($val) && strlen($val) > 10) {
                $found = $val;
            }
        });

        return $found;
    }
}