@extends('landlord.layout.main') @section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if($errors->has('image'))
<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('image') }}</div>
@endif

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            {!! Form::open(['route' => 'heroSection.store', 'files' => true, 'method' => 'post']) !!}
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.Hero Section')}}</h4>
                    </div>
                    <div class="card-body collapse show" id="gs_collapse">

                        <ul class="nav nav-tabs mb-3" role="tablist">
                            @foreach($language_all as $language)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" href="#lang-{{ $language->id }}" role="tab">
                                        {{ $language->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach($language_all as $language)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="lang-{{ $language->id }}" role="tabpanel">
                                    <input type="hidden" name="lang_ids[]" value="{{ $language->id }}">

                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>{{ __('db.Heading') }} ({{ $language->name }}) *</label>
                                                <input type="text" name="heading[]" class="form-control" value="{{ $heroes[$language->id]->heading ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>{{ __('db.Sub Heading') }} ({{ $language->name }}) *</label>
                                                <input type="text" name="sub_heading[]" class="form-control" value="{{ $heroes[$language->id]->sub_heading ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>{{ __('db.Button Text') }} ({{ $language->name }}) *</label>
                                                <input type="text" name="button_text[]" class="form-control" value="{{ $heroes[$language->id]->button_text ?? '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr>

                        <!-- Common Image Input -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('db.Hero Image') }} (Common for all languages)  *</label>
                                    <input type="file" name="image" class="form-control" />
                                </div>

                                @if(!empty(optional($heroes->first())->image)) {{-- Optional: Show existing image --}}
                                    <div class="mt-2">
                                        <img src="{{ asset('landlord/images/' . optional($heroes->first())->image) }}" style="max-width: 100%;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" value="{{__('db.Save')}}" class="btn btn-primary">
                        </div>





                    </div>
                </div>
            {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')

<script>
    $(document).ready(function() {
        $("ul#cms").siblings('a').attr('aria-expanded','true');
        $("ul#cms").addClass("show");
        $("ul#cms #cms-hero-menu").addClass("active");
    });

</script>

@endpush
