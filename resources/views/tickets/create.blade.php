@php
    $layout = 'backend.layout.main';
@endphp

@extends($layout)

@section('title','Create Tickets')
@section('content')

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible text-center">
        <button type="button" class="close" data-dismiss="alert"
            aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{ __('db.create_ticket') }}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{ __('db.The field labels marked with * are required input fields') }}.</small></p>
                        {!! Form::open(['route' => 'tickets.store', 'method' => 'post']) !!}
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>{{ __('db.subject') }} *</label>
                                    <input type="text" name="subject" class="form-control" required>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label>{{ __('db.description') }} *</label>
                                    <textarea name="description" rows="4" class="description form-control"></textarea>
                                    <p class="italic"><small>{{ __('db.insert_upload_text') }} - <a target="_blank" href="https://snipboard.io/">snipborad.io</a> / <a target="_blank" href="https://streamable.com/">streamable.com</a></small></p>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary">{{ __('db.submit') }}</button>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script type="text/javascript">
    tinymce.init({
        selector: '.description',
        height: 130,
        menubar: false,
        plugins: 'link',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | removeformat',
        branding: false
    });
</script>
@endpush
