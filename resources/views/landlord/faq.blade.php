@extends('landlord.layout.main') @section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
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
            {!! Form::open(['route' => 'faqSection.store', 'files' => true, 'method' => 'post']) !!}
                <!-- HEADINGS PER LANGUAGE -->
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.FAQ Section')}}</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach ($language_all as $index => $lang)
                                <li class="nav-item">
                                    <button class="nav-link @if($index === 0) active @endif" data-toggle="tab" data-target="#heading-lang-{{ $lang->id }}">
                                        {{ $lang->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach ($language_all as $index => $lang)
                                <div class="tab-pane fade @if($index === 0) show active @endif p-3" id="heading-lang-{{ $lang->id }}">
                                    <div class="form-group mb-2">
                                        <label>{{__('db.Heading')}} ({{ $lang->name }}) *</label>
                                        <input type="text" class="form-control" name="descriptions[{{ $lang->id }}][heading]" value="{{ $faq_description[$lang->id]->heading ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{__('db.Sub Heading')}} ({{ $lang->name }}) *</label>
                                        <input type="text" class="form-control" name="descriptions[{{ $lang->id }}][sub_heading]" value="{{ $faq_description[$lang->id]->sub_heading ?? '' }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- FAQS -->
                <div id="faqs-wrapper">
                    @foreach($faqs as $fIndex => $group)
                        @php $first = $group->first(); @endphp
                        <div class="faq-block border p-3 mb-4" data-index="{{ $fIndex }}">
                            <div class="row">
                                <div class="col-md-10">
                                    <ul class="nav nav-tabs" role="tablist">
                                        @foreach ($language_all as $i => $lang)
                                            <li class="nav-item">
                                                <button class="nav-link @if($i === 0) active @endif" data-toggle="tab" data-target="#faq-{{ $fIndex }}-lang-{{ $lang->id }}">
                                                    {{ $lang->name }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content border border-top-0 p-3">
                                        @foreach ($language_all as $i => $lang)
                                            @php $translation = $group->get($lang->id); @endphp
                                            <div class="tab-pane fade @if($i === 0) show active @endif" id="faq-{{ $fIndex }}-lang-{{ $lang->id }}">
                                                <input type="hidden" name="faqs[{{ $fIndex }}][translations][{{ $lang->id }}][lang_id]" value="{{ $lang->id }}">
                                                <div class="form-group">
                                                    <label>{{__('db.Question')}} ({{ $lang->name }}) *</label>
                                                    <input type="text" name="faqs[{{ $fIndex }}][translations][{{ $lang->id }}][question]" class="form-control" value="{{ $translation->question ?? '' }}">
                                                </div>
                                                <div class="form-group mt-2">
                                                    <label>{{__('db.Answer')}} ({{ $lang->name }}) *</label>
                                                    <textarea name="faqs[{{ $fIndex }}][translations][{{ $lang->id }}][answer]" class="form-control">{{ $translation->answer ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-danger float-end remove-faq">&times;</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-outline-info" id="add-faq">+ {{__('db.Add Faq')}}</button>
                <button type="submit" class="btn btn-primary">{{__('db.Save')}}</button>
            {!! Form::close() !!}
            </div>
        </div>
    </div>

</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#cms").siblings('a').attr('aria-expanded','true');
    $("ul#cms").addClass("show");
    $("ul#cms #cms-faq-menu").addClass("active");

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var languages = @json($language_all);

    var faqIndex = {{ count($faqs) }}; // Start from last index used

    $(document).on('click', '#add-faq', function () {
        let block = `
            <div class="faq-block border p-3 mb-4" data-index="${faqIndex}">
                <div class="row">
                    <div class="col-md-10">
                        <ul class="nav nav-tabs" role="tablist">
                            ${languages.map((lang, i) => `
                                <li class="nav-item">
                                    <button class="nav-link ${i === 0 ? 'active' : ''}"
                                            data-toggle="tab"
                                            data-target="#faq-${faqIndex}-lang-${lang.id}">
                                        ${lang.name}
                                    </button>
                                </li>`).join('')}
                        </ul>
                        <div class="tab-content border border-top-0 p-3">
                            ${languages.map((lang, i) => `
                                <div class="tab-pane fade ${i === 0 ? 'show active' : ''}"
                                    id="faq-${faqIndex}-lang-${lang.id}">
                                    <input type="hidden" name="faqs[${faqIndex}][translations][${lang.id}][lang_id]" value="${lang.id}">
                                    <div class="form-group">
                                        <label>{{__('db.Question')}} (${lang.name}) *</label>
                                        <input type="text" name="faqs[${faqIndex}][translations][${lang.id}][question]" class="form-control">
                                    </div>
                                    <div class="form-group mt-2">
                                        <label>{{__('db.Answer')}} (${lang.name}) *</label>
                                        <textarea name="faqs[${faqIndex}][translations][${lang.id}][answer]" class="form-control"></textarea>
                                    </div>
                                </div>`).join('')}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger float-end remove-faq">&times;</button>
                    </div>
                </div>
            </div>
        `;
        $('#faqs-wrapper').append(block);
        faqIndex++;
    });

    $(document).on("click", ".remove-faq", function() {
        $(this).closest('.faq-block').remove();
    });

    $( "#faqs-wrapper" ).sortable({
      items: ".faq-block",
      cursor: 'move',
      opacity: 0.6,
    });

</script>
@endpush
