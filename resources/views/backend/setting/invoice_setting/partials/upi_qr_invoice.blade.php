{{--
    UPI scan-to-pay QR (tenant invoice_settings.upi_id).
    Expects: $upiQrText, $upiIdDisplay (from SaleController::genInvoice).
    Optional: $upiQrMaxWidth, $upiQrModuleW/H (pixel module size for DNS2D), $upiQrType, $upiQrThermal (compact thermal styling).
--}}
@if (!empty($upiQrText))
    @php
        $qrType = (string) ($upiQrType ?? 'QRCODE,M');
        if ($qrType === '') {
            $qrType = 'QRCODE,M';
        }
        $modW = (int) ($upiQrModuleW ?? 3);
        $modH = (int) ($upiQrModuleH ?? 3);
        $modW = max(1, min(8, $modW));
        $modH = max(1, min(8, $modH));
        $upiPng = DNS2D::getBarcodePNG($upiQrText, $qrType, $modW, $modH);
        $isThermal = !empty($upiQrThermal);
        $titleSize = $upiQrTitleSize ?? ($isThermal ? '9px' : '11px');
        $vpaSize = $upiQrVpaSize ?? ($isThermal ? '8px' : '11px');
        $titleText = $upiQrTitle ?? __('Pay with UPI');
    @endphp
    @if (!empty($upiPng))
    <div class="upi-qr-invoice text-center"
        style="margin-top:{{ $isThermal ? '6px' : '12px' }};padding:{{ $isThermal ? '4px 2px' : '8px 4px' }};width:100%;box-sizing:border-box;">
        <div style="font-size:{{ $titleSize }};margin-bottom:{{ $isThermal ? '3px' : '6px' }};font-weight:600;line-height:1.2;">
            {{ $titleText }}</div>
        <img class="upi-qr-img"
            style="max-width:{{ $upiQrMaxWidth ?? '128px' }};width:100%;height:auto;display:block;margin:0 auto;image-rendering:pixelated;image-rendering:-moz-crisp-edges;-ms-interpolation-mode:nearest-neighbor;"
            src="data:image/png;base64,{{ $upiPng }}"
            alt="UPI QR" />
        @if (!empty($upiIdDisplay))
            <div style="font-size:{{ $vpaSize }};margin-top:{{ $isThermal ? '3px' : '6px' }};word-break:break-all;line-height:1.25;max-width:100%;padding:0 2px;">
                <strong>{{ $upiIdDisplay }}</strong></div>
        @endif
    </div>
    @endif
@endif
