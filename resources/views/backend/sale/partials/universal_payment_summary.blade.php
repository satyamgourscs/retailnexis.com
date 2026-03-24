{{-- Shared payment summary: hidden payment_status (submitted) + disabled status dropdown (synced by JS). Include once per sales form. --}}
@php
    $__salePs = old('payment_status');
    if ($__salePs === null && isset($lims_sale_data) && !empty($lims_sale_data->payment_status)) {
        $__salePs = (string) $lims_sale_data->payment_status;
    }
    if ($__salePs === null) {
        $__salePs = '1';
    }
    $__salePs = (string) $__salePs;
    // POS/legacy "Due" (2) → show as Pending in the simplified trio
    if ($__salePs === '2') {
        $__salePs = '1';
    }
    if (! in_array($__salePs, ['1', '3', '4'], true)) {
        $__salePs = '1';
    }
@endphp
<input type="hidden" name="payment_status" id="payment_status" value="{{ $__salePs }}">
<x-validation-error fieldName="payment_status" />
<div class="row mt-2">
    <div class="col-12">
        <div class="card border shadow-sm mb-2" id="sale-universal-payment-summary">
            <div class="card-body py-3 px-3">
                <strong class="d-block mb-3">{{ __('db.Payment') }}</strong>
                <div class="row g-2 align-items-end">
                    <div class="col-6 col-md-3">
                        <label class="small text-muted mb-0 d-block" for="sale_payment_status_display">{{ __('db.Payment Status') }}</label>
                        <select id="sale_payment_status_display" class="form-control form-control-sm bg-light" data-sale-payment-line="1" disabled title="{{ __('db.Updates automatically from amounts') }}" aria-disabled="true">
                            <option value="1">{{ __('db.Pending') }}</option>
                            <option value="3">{{ __('db.Partial') }}</option>
                            <option value="4">{{ __('db.Paid') }}</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="small text-muted mb-0 d-block">Total due</label>
                        <input type="text" class="form-control form-control-sm" id="sale_payment_total_due" readonly autocomplete="off">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="small text-muted mb-0 d-block">Amount received</label>
                        <input type="number" step="any" min="0" class="form-control form-control-sm" id="sale_payment_amount_received" value="{{ number_format(0, $general_setting->decimal, '.', '') }}" autocomplete="off">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="small text-muted mb-0 d-block">Remaining due</label>
                        <input type="text" class="form-control form-control-sm" id="sale_payment_remaining_due" readonly autocomplete="off">
                    </div>
                    <div class="col-12 mt-1">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="sale_payment_fully_paid" value="1">
                            <label class="form-check-label small" for="sale_payment_fully_paid">Fully paid</label>
                        </div>
                    </div>
                </div>
                <small class="text-muted d-block mt-2 mb-0">Optional: split by payment method below. Total received cannot exceed total due.</small>
            </div>
        </div>
    </div>
</div>
