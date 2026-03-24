<?php

namespace App\Support;

/**
 * Build NPCI-style UPI payment URI for QR encoding (scan-to-pay).
 */
final class UpiUri
{
    /**
     * @param  string  $upiId  Virtual Payment Address, e.g. merchant@paytm
     * @param  string  $payeeName  Display name (sanitized)
     * @param  float|null  $amount  Optional preset amount in INR
     */
    public static function build(string $upiId, string $payeeName = 'Merchant', ?float $amount = null, string $currency = 'INR'): string
    {
        $upiId = trim($upiId);
        $payeeName = self::sanitizePayeeName($payeeName);

        $query = [
            'pa' => $upiId,
            'pn' => $payeeName !== '' ? $payeeName : 'Merchant',
            'cu' => $currency,
        ];

        if ($amount !== null && $amount > 0) {
            $query['am'] = number_format($amount, 2, '.', '');
        }

        return 'upi://pay?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    private static function sanitizePayeeName(string $name): string
    {
        $name = preg_replace('/[^\p{L}\p{N}\s\-_.]/u', '', $name) ?? '';
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');

        return mb_substr($name, 0, 99);
    }
}
