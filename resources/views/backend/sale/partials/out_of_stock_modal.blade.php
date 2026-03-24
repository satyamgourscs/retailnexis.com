{{-- Shared "Out of Stock" modal — used on Add/Edit Sale (all tenants). Bootstrap 4 compatible. --}}
<div class="modal fade" id="outOfStockModal" tabindex="-1" role="dialog" aria-labelledby="outOfStockModalLabel"
    aria-hidden="true" data-backdrop="true" data-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg oos-modal-content">
            <div class="modal-header border-0 oos-modal-header text-white">
                <h5 class="modal-title font-weight-bold" id="outOfStockModalLabel">{{ __('db.Out of Stock') }}</h5>
                <button type="button" class="close text-white oos-modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0 lead" data-oos-message data-default="{{ __('db.Out of Stock') }}">{{ __('db.Out of Stock') }}</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary px-4 font-weight-bold" data-dismiss="modal"
                    id="outOfStockModalOk">OK</button>
            </div>
        </div>
    </div>
</div>
