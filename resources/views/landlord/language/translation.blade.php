@extends('landlord.layout.main') @section('content')

    <x-success-message key="message" />
    <x-error-message key="not_permitted" />
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section>
        <div class="container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ __('db.translations') }}: {{ $langCode }}</h2>
                <input type="text" id="searchTranslation" class="form-control w-50" placeholder="{{ __('db.search_ranslation') }}...">
                <a href="{{ route('languages.index', [], false) }}" class="btn btn-primary">{{ __('db.manage_languages') }}</a>
            </div>

            {!! Form::open([
                'url' => route('languages.updateTranslation', $langCode, false),
                'method' => 'post',
            ]) !!}
            <table class="table table-bordered">
                <thead class="table-secondary" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th class="col-4">{{ __('db.key') }}</th>
                        <th class="d-flex justify-content-between align-items-center">
                        {{ __('db.value') }}
                        <button type="submit" class="btn btn-warning btn-sm update-btn">
                            {{ __('db.update') }}
                        </button>
                        </th>
                    </tr>
                </thead>
                <tbody id="translation_list">
                    @foreach ($translations as $key => $value)
                        <tr>
                            <td class="translation-key">{{ $key }}</td>
                            <td>
                                <input class="form-control" type="text" name="translations[{{ $key }}]"
                                    value="{{ $value }}">
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            {!! Form::close() !!}
        </div>
    </section>

@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            let debounceTimer;
            $('#searchTranslation').on('keyup', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const value = $(this).val().toLowerCase().trim();
                    $('#translation_list tr').each(function() {
                        const key = $(this).find('.translation-key').text().toLowerCase();
                        const val = $(this).find('input[type="text"]').val().toLowerCase();
                        $(this).toggle(key.includes(value) || val.includes(value));
                    });
                }, 150);
            });
        });
    </script>
@endpush
