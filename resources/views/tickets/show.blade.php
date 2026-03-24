@php
if (!tenant()) {
    $routePrefix = 'superadmin.';
    $layout = 'landlord.layout.main';
    $cssStackName = 'custom-css';
}
else {
    $routePrefix = '';
    $layout = 'backend.layout.main';
    $cssStackName = 'css';
}
@endphp

@extends($layout)

@push($cssStackName)
    <style>
        .ticket {
            background-color: #f0f2f5;
            border-radius: 5px;
            box-shadow: 0 3px 3px rgba(0, 0, 0, 0.2);
            color: #333;
            overflow: auto;
            padding: 25px;
        }

        .ticket.border {
            border: 1px solid #00a884 !important;
        }

        .ticket.parent {
            border-left: 5px solid #00a884;
            padding-left: 30px;
        }

        .ticket.reply {
            background-color: #d9fdd3;
        }

        .ticket span {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .ticket p {
            font-size: 15px;
            display: -webkit-box;
            margin-bottom: 0;
            margin-top: 7px;
        }

        .ticket p.short {
            overflow: hidden;
            text-overflow: ellipsis;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }
    </style>
@endpush

@section('title', 'Show Tickets')
@section('content')

    <x-success-message key="message" />
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

    <section>
        <div class="container-fluid">
            <h1><strong>Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</strong>
            </h1>
            <hr>
        </div>

        <div class="container-fluid">
            <div class="ticket parent mb-3">
                <span>{{ $ticket->created_at->format($general_setting->date_format . ' h:i A') }}</span>
                <p>{!! $ticket->description !!}</p>
            </div>
            @foreach ($replies as $reply)
                <div class="ticket @if ($reply->superadmin == 1) reply @endif mb-3">
                    <span>{{ $reply->created_at->format($general_setting->date_format . ' h:i A') }}</span>
                    <p>{!! $reply->description !!}</p>
                </div>
            @endforeach

            {!! Form::open(['route' => [$routePrefix.'tickets.reply', $ticket->id], 'method' => 'post']) !!}
            <div class="row mt-5">
                <div class="col-md-12 form-group">
                    <textarea name="description" rows="4" class="description form-control"></textarea>
                    <p class="italic"><small>{{ __('db.insert_upload_text') }} - <a target="_blank"
                                href="https://snipboard.io/">snipborad.io</a> / <a target="_blank"
                                href="https://streamable.com/">streamable.com</a></small></p>
                </div>
                <div class="col-md-12 mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('db.submit') }}</button>
                </div>
            </div>
            {!! Form::close() !!}
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
