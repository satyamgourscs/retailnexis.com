@extends('landlord.layout.main') @section('content')

@push('custom-css')
    <style>
        #icons {
            opacity: 0;
            visibility: hidden;
        }
        .icon_collapse {
            height: 50vh;
            margin-top: 15px;
            overflow-y: scroll;
        }
        .icon_collapse section {
            padding: 30px 0 0;
        }
        .icon_collapse .card {
            background: #e5e6e7;
            margin: 0 15px;
        }
        .page-header {
            border-bottom: 1px solid #555;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .fa-hover {
            text-align: center;
            margin-bottom: 15px
        }
        .fa-hover a {
            cursor: pointer;
            font-size: 0px;
        }
        .fa-hover i:hover {
            opacity: 0.8;
        }
        .fa-hover i {
            color: #7c5cc4;
            display: block;
            font-size: 20px;
        }
        .ticket{
            background: #f5f6f7;
            border-radius: 5px;
            margin-bottom: 30px;
            padding: 20px;
        }
        .ticket p {
            color: #333 !important
        }
        .parent{
            border:1px solid #ddd;
        }
        .ticket-meta {
            border-bottom: 1px solid #ddd;
            font-size: 12px;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }
        .ticket-meta span {
            color: #333;
        }
        .reply {
            background: #e5f4fb
        }
    </style>
@endpush

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="textFadeUp mb-5">
                        Ticket Details
                    </h1>

                    @if(isset($parent))
                    <div class="ticket parent mb-4">
                        <div class="ticket-meta">
                            <span>Ticket No. #{{ $parent->id }}</span> | <span><i class="dripicons-calendar"></i> {{ date('d M Y', strtotime($parent->created_at)) }}</span>
                        </div>
                        <h3 class="mt-2 mb-2">
                        {{ $parent->subject }}
                        </h3>
                        <p class="mt-2 ">{!! htmlspecialchars_decode($parent->description) !!}</p>
                    </div>
                    @endif
                    @if(isset($children))
                        @foreach ($children as $ticket)
                            @if($ticket->superadmin == 0)
                            <div class="ticket mb-4">
                                <div class="ticket-meta">
                                    <span>{{ $ticket->tenant_id }}</span> said on <span><i class="dripicons-calendar"></i> {{ date('d M Y', strtotime($ticket->created_at)) }}</span>
                                </div>
                            @else
                            <div class="ticket reply mb-4">
                                <div class="ticket-meta">
                                    <span>Superadmin</span> said on <span><i class="dripicons-calendar"></i> {{ date('d M Y', strtotime($ticket->created_at)) }}</span>
                                </div>
                            @endif
                                <p>{!! htmlspecialchars_decode($ticket->description) !!}</p>
                            </div>
                        @endforeach
                    @endif
                    @if($parent->status == 1)
                    <form class="mt-5" method="POST" action="{{ url('superadmin/support/ticket/store') }}" id="create_ticket_form">
                        @csrf
                        <textarea class="form-control" name="description" id="ticket_details" placeholder="Add comments *" minlength="2"></textarea>
                        <input type="hidden" name="subject" value="{{$parent->subject}}">
                        <input type="hidden" name="tenant_id" value="{{$parent->tenant_id}}">
                        <input type="hidden" name="parent_ticket_id" value="{{$parent->id}}">
                        <div class="text-center mt-1">
                            <i class="dripicons-paperclip"></i> <small> Please insert your images/video links in above text box, by uploading to - <a href="https://snipboard.io/">snipborad.io</a> / <a href="https://streamable.com/upload-video">streamable.com</a></small>
                        </div>
                        <button class="d-block btn btn-primary mt-5" type="submit" id="create_ticket">Post</button>
                    </form>
                    @else
                    <div class="text-center">
                        <p class="mt-3">Please reopen this ticket to make a comment.
                            <form class="d-inline" method="post" action="">
                                @csrf
                                <input type="hidden" value="{{ $parent->id }}" name="ticket_id">
                                <button type="submit" class="btn mt-0"><i class="dripicons-return"></i></button>
                            </form>
                        </p>
                    </div>
                    @endif
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
        selector: 'textarea',  // change this value according to your HTML
        menubar: '',
        branding: false,
        plugins: [
          'advlist autolink link image lists charmap print preview hr anchor pagebreak',
        ],
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
          ' | link image | ',
    });
</script>
@endpush
