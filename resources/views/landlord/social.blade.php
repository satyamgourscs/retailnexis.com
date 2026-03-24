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
            {!! Form::open(['url' => route('social.store', [], false), 'files' => true, 'method' => 'post']) !!}
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{__('db.Social Section')}}</h4>
                    </div>
                    <div class="card-body collapse show" id="gs_collapse">
                        <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}.</small></p>
                        <div id="custom-field">
                            <div class="row">
                                <div class="col-sm-3">
                                    <input type="text" name="icon[]" class="form-control icon" value="" data-toggle="collapse" href="#icon_collapse" aria-expanded="false" aria-controls="icon_collapse" placeholder="{{__('db.Click to choose icon')}}"/>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="name[]" class="form-control" placeholder="{{__('db.name')}}"/>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" name="link[]" class="form-control" placeholder="{{__('db.Link')}}"/>
                                </div>
                                <div class="collapse icon_collapse" id="icon_collapse">
                                    <div class="card">
                                        <div class="card-body">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-info mar-bot-30" id="custom-btn" type="button">+ {{__('db.Add More')}}</button>
                                <button type="submit" class="btn btn-primary mar-bot-30">{{__('db.Submit')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="social-table" class="table table-striped">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{__('db.Icon')}}</th>
                    <th>{{__('db.Name')}}</th>
                    <th class="not-exported">{{__('db.Action')}}</th>
                </tr>
            </thead>
            <tbody id="tablecontents">
                @if($socials)
                @foreach($socials as $key=>$social)
                <tr class="row1" data-id="{{$social->id}}">
                    <td>{{$key}}</td>
                    <td><i class="{{ $social->icon }}"></i> {{ $social->icon }}</td>
                    <td>{{ $social->name }}</td>
                    <td>
                        <button type="button" data-font="{{$social->icon}}" data-id="{{$social->id}}" data-name="{{$social->name}}" data-link="{{$social->link}}" class="edit-btn btn btn-link" data-toggle="modal" data-target="#editModal" ><i class="dripicons-document-edit"></i></button>
                        <form class="d-inline" method="post" action="{{ route('social.delete', $social->id, false) }}">
                        @csrf
                            <button type="submit" class="btn btn-link" onclick="return confirm('{{__('db.Are you sure you want to delete?')}}')"><i class="dripicons-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- edit Modal -->
    <div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('social.update', [], false) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{__('db.Update Social')}}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>{{__('db.The field labels marked with * are required input fields')}}</small></p>
                    <form>
                        <div class="form-group">
                            <label><strong>{{__('db.Icon')}} *</strong></label>
                            <input type="text" name="icon" class="form-control icon" value="" data-toggle="collapse" href="#icon_collapse_edit" aria-expanded="false" aria-controls="icon_collapse_edit"/>
                        </div>
                        <div class="collapse icon_collapse" id="icon_collapse_edit">
                            <div class="card">
                                <div class="card-body">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Name')}} *</strong></label>
                            <input type="text" class="form-control" name="name" required />
                        </div>
                        <div class="form-group">
                            <label><strong>{{__('db.Link')}} *</strong></label>
                            <input type="text" class="form-control" name="link" required />
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="social_id">
                            <input type="submit" value="{{__('db.Submit')}}" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </form>
        </div>
        </div>
    </div>

    <div id="icons" class="container-fluid">
        <section id="brand">
            <h2 class="page-header">Brand Icons</h2>
            <div class="row fontawesome-icon-list margin-bottom-lg">



                <div class="fa-hover col-sm-1"><a data-font-icon="500px"><i class="fa fa-500px" aria-hidden="true"></i> <span class="sr-only">Example of </span>500px</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="adn"><i class="fa fa-adn" aria-hidden="true"></i> <span class="sr-only">Example of </span>adn</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="amazon"><i class="fa fa-amazon" aria-hidden="true"></i> <span class="sr-only">Example of </span>amazon</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="android"><i class="fa fa-android" aria-hidden="true"></i> <span class="sr-only">Example of </span>android</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="angellist"><i class="fa fa-angellist" aria-hidden="true"></i> <span class="sr-only">Example of </span>angellist</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="apple"><i class="fa fa-apple" aria-hidden="true"></i> <span class="sr-only">Example of </span>apple</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="bandcamp"><i class="fa fa-bandcamp" aria-hidden="true"></i> <span class="sr-only">Example of </span>bandcamp</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="behance"><i class="fa fa-behance" aria-hidden="true"></i> <span class="sr-only">Example of </span>behance</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="behance-square"><i class="fa fa-behance-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>behance-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="bitbucket"><i class="fa fa-bitbucket" aria-hidden="true"></i> <span class="sr-only">Example of </span>bitbucket</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="bitbucket-square"><i class="fa fa-bitbucket-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>bitbucket-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="btc"><i class="fa fa-bitcoin" aria-hidden="true"></i> <span class="sr-only">Example of </span>bitcoin <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="black-tie"><i class="fa fa-black-tie" aria-hidden="true"></i> <span class="sr-only">Example of </span>black-tie</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="bluetooth"><i class="fa fa-bluetooth" aria-hidden="true"></i> <span class="sr-only">Example of </span>bluetooth</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="bluetooth-b"><i class="fa fa-bluetooth-b" aria-hidden="true"></i> <span class="sr-only">Example of </span>bluetooth-b</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="btc"><i class="fa fa-btc" aria-hidden="true"></i> <span class="sr-only">Example of </span>btc</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="buysellads"><i class="fa fa-buysellads" aria-hidden="true"></i> <span class="sr-only">Example of </span>buysellads</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="cc-amex"><i class="fa fa-cc-amex" aria-hidden="true"></i> <span class="sr-only">Example of </span>cc-amex</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="cc-diners-club"><i class="fa fa-cc-diners-club" aria-hidden="true"></i> <span class="sr-only">Example of </span>cc-diners-club</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="cc-discover"><i class="fa fa-cc-discover" aria-hidden="true"></i> <span class="sr-only">Example of </span>cc-discover</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="cc-jcb"><i class="fa fa-cc-jcb" aria-hidden="true"></i> <span class="sr-only">Example of </span>cc-jcb</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="cc-mastercard"><i class="fa fa-cc-mastercard" aria-hidden="true"></i> <span class="sr-only">Example of </span>cc-mastercard</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="cc-paypal"><i class="fa fa-cc-paypal" aria-hidden="true"></i> <span class="sr-only">Example of </span>cc-paypal</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="cc-stripe"><i class="fa fa-cc-stripe" aria-hidden="true"></i> <span class="sr-only">Example of </span>cc-stripe</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="cc-visa"><i class="fa fa-cc-visa" aria-hidden="true"></i> <span class="sr-only">Example of </span>cc-visa</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="chrome"><i class="fa fa-chrome" aria-hidden="true"></i> <span class="sr-only">Example of </span>chrome</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="codepen"><i class="fa fa-codepen" aria-hidden="true"></i> <span class="sr-only">Example of </span>codepen</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="codiepie"><i class="fa fa-codiepie" aria-hidden="true"></i> <span class="sr-only">Example of </span>codiepie</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="connectdevelop"><i class="fa fa-connectdevelop" aria-hidden="true"></i> <span class="sr-only">Example of </span>connectdevelop</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="contao"><i class="fa fa-contao" aria-hidden="true"></i> <span class="sr-only">Example of </span>contao</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="css3"><i class="fa fa-css3" aria-hidden="true"></i> <span class="sr-only">Example of </span>css3</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="dashcube"><i class="fa fa-dashcube" aria-hidden="true"></i> <span class="sr-only">Example of </span>dashcube</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="delicious"><i class="fa fa-delicious" aria-hidden="true"></i> <span class="sr-only">Example of </span>delicious</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="deviantart"><i class="fa fa-deviantart" aria-hidden="true"></i> <span class="sr-only">Example of </span>deviantart</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="digg"><i class="fa fa-digg" aria-hidden="true"></i> <span class="sr-only">Example of </span>digg</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="dribbble"><i class="fa fa-dribbble" aria-hidden="true"></i> <span class="sr-only">Example of </span>dribbble</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="dropbox"><i class="fa fa-dropbox" aria-hidden="true"></i> <span class="sr-only">Example of </span>dropbox</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="drupal"><i class="fa fa-drupal" aria-hidden="true"></i> <span class="sr-only">Example of </span>drupal</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="edge"><i class="fa fa-edge" aria-hidden="true"></i> <span class="sr-only">Example of </span>edge</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="eercast"><i class="fa fa-eercast" aria-hidden="true"></i> <span class="sr-only">Example of </span>eercast</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="empire"><i class="fa fa-empire" aria-hidden="true"></i> <span class="sr-only">Example of </span>empire</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="envira"><i class="fa fa-envira" aria-hidden="true"></i> <span class="sr-only">Example of </span>envira</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="etsy"><i class="fa fa-etsy" aria-hidden="true"></i> <span class="sr-only">Example of </span>etsy</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="expeditedssl"><i class="fa fa-expeditedssl" aria-hidden="true"></i> <span class="sr-only">Example of </span>expeditedssl</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="font-awesome"><i class="fa fa-fa" aria-hidden="true"></i> <span class="sr-only">Example of </span>fa <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="facebook"><i class="fa fa-facebook" aria-hidden="true"></i> <span class="sr-only">Example of </span>facebook</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="facebook"><i class="fa fa-facebook-f" aria-hidden="true"></i> <span class="sr-only">Example of </span>facebook-f <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="facebook-official"><i class="fa fa-facebook-official" aria-hidden="true"></i> <span class="sr-only">Example of </span>facebook-official</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="facebook-square"><i class="fa fa-facebook-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>facebook-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="firefox"><i class="fa fa-firefox" aria-hidden="true"></i> <span class="sr-only">Example of </span>firefox</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="first-order"><i class="fa fa-first-order" aria-hidden="true"></i> <span class="sr-only">Example of </span>first-order</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="flickr"><i class="fa fa-flickr" aria-hidden="true"></i> <span class="sr-only">Example of </span>flickr</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="font-awesome"><i class="fa fa-font-awesome" aria-hidden="true"></i> <span class="sr-only">Example of </span>font-awesome</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="fonticons"><i class="fa fa-fonticons" aria-hidden="true"></i> <span class="sr-only">Example of </span>fonticons</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="fort-awesome"><i class="fa fa-fort-awesome" aria-hidden="true"></i> <span class="sr-only">Example of </span>fort-awesome</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="forumbee"><i class="fa fa-forumbee" aria-hidden="true"></i> <span class="sr-only">Example of </span>forumbee</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="foursquare"><i class="fa fa-foursquare" aria-hidden="true"></i> <span class="sr-only">Example of </span>foursquare</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="free-code-camp"><i class="fa fa-free-code-camp" aria-hidden="true"></i> <span class="sr-only">Example of </span>free-code-camp</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="empire"><i class="fa fa-ge" aria-hidden="true"></i> <span class="sr-only">Example of </span>ge <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="get-pocket"><i class="fa fa-get-pocket" aria-hidden="true"></i> <span class="sr-only">Example of </span>get-pocket</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="gg"><i class="fa fa-gg" aria-hidden="true"></i> <span class="sr-only">Example of </span>gg</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="gg-circle"><i class="fa fa-gg-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>gg-circle</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="git"><i class="fa fa-git" aria-hidden="true"></i> <span class="sr-only">Example of </span>git</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="git-square"><i class="fa fa-git-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>git-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="github"><i class="fa fa-github" aria-hidden="true"></i> <span class="sr-only">Example of </span>github</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="github-alt"><i class="fa fa-github-alt" aria-hidden="true"></i> <span class="sr-only">Example of </span>github-alt</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="github-square"><i class="fa fa-github-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>github-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="gitlab"><i class="fa fa-gitlab" aria-hidden="true"></i> <span class="sr-only">Example of </span>gitlab</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="gratipay"><i class="fa fa-gittip" aria-hidden="true"></i> <span class="sr-only">Example of </span>gittip <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="glide"><i class="fa fa-glide" aria-hidden="true"></i> <span class="sr-only">Example of </span>glide</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="glide-g"><i class="fa fa-glide-g" aria-hidden="true"></i> <span class="sr-only">Example of </span>glide-g</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="google"><i class="fa fa-google" aria-hidden="true"></i> <span class="sr-only">Example of </span>google</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="google-plus"><i class="fa fa-google-plus" aria-hidden="true"></i> <span class="sr-only">Example of </span>google-plus</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="google-plus-official"><i class="fa fa-google-plus-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>google-plus-circle <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="google-plus-official"><i class="fa fa-google-plus-official" aria-hidden="true"></i> <span class="sr-only">Example of </span>google-plus-official</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="google-plus-square"><i class="fa fa-google-plus-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>google-plus-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="google-wallet"><i class="fa fa-google-wallet" aria-hidden="true"></i> <span class="sr-only">Example of </span>google-wallet</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="gratipay"><i class="fa fa-gratipay" aria-hidden="true"></i> <span class="sr-only">Example of </span>gratipay</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="grav"><i class="fa fa-grav" aria-hidden="true"></i> <span class="sr-only">Example of </span>grav</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="hacker-news"><i class="fa fa-hacker-news" aria-hidden="true"></i> <span class="sr-only">Example of </span>hacker-news</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="houzz"><i class="fa fa-houzz" aria-hidden="true"></i> <span class="sr-only">Example of </span>houzz</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="html5"><i class="fa fa-html5" aria-hidden="true"></i> <span class="sr-only">Example of </span>html5</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="imdb"><i class="fa fa-imdb" aria-hidden="true"></i> <span class="sr-only">Example of </span>imdb</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="instagram"><i class="fa fa-instagram" aria-hidden="true"></i> <span class="sr-only">Example of </span>instagram</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="internet-explorer"><i class="fa fa-internet-explorer" aria-hidden="true"></i> <span class="sr-only">Example of </span>internet-explorer</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="ioxhost"><i class="fa fa-ioxhost" aria-hidden="true"></i> <span class="sr-only">Example of </span>ioxhost</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="joomla"><i class="fa fa-joomla" aria-hidden="true"></i> <span class="sr-only">Example of </span>joomla</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="jsfiddle"><i class="fa fa-jsfiddle" aria-hidden="true"></i> <span class="sr-only">Example of </span>jsfiddle</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="lastfm"><i class="fa fa-lastfm" aria-hidden="true"></i> <span class="sr-only">Example of </span>lastfm</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="lastfm-square"><i class="fa fa-lastfm-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>lastfm-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="leanpub"><i class="fa fa-leanpub" aria-hidden="true"></i> <span class="sr-only">Example of </span>leanpub</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="linkedin"><i class="fa fa-linkedin" aria-hidden="true"></i> <span class="sr-only">Example of </span>linkedin</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="linkedin-square"><i class="fa fa-linkedin-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>linkedin-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="linode"><i class="fa fa-linode" aria-hidden="true"></i> <span class="sr-only">Example of </span>linode</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="linux"><i class="fa fa-linux" aria-hidden="true"></i> <span class="sr-only">Example of </span>linux</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="maxcdn"><i class="fa fa-maxcdn" aria-hidden="true"></i> <span class="sr-only">Example of </span>maxcdn</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="meanpath"><i class="fa fa-meanpath" aria-hidden="true"></i> <span class="sr-only">Example of </span>meanpath</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="medium"><i class="fa fa-medium" aria-hidden="true"></i> <span class="sr-only">Example of </span>medium</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="meetup"><i class="fa fa-meetup" aria-hidden="true"></i> <span class="sr-only">Example of </span>meetup</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="mixcloud"><i class="fa fa-mixcloud" aria-hidden="true"></i> <span class="sr-only">Example of </span>mixcloud</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="modx"><i class="fa fa-modx" aria-hidden="true"></i> <span class="sr-only">Example of </span>modx</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="odnoklassniki"><i class="fa fa-odnoklassniki" aria-hidden="true"></i> <span class="sr-only">Example of </span>odnoklassniki</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="odnoklassniki-square"><i class="fa fa-odnoklassniki-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>odnoklassniki-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="opencart"><i class="fa fa-opencart" aria-hidden="true"></i> <span class="sr-only">Example of </span>opencart</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="openid"><i class="fa fa-openid" aria-hidden="true"></i> <span class="sr-only">Example of </span>openid</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="opera"><i class="fa fa-opera" aria-hidden="true"></i> <span class="sr-only">Example of </span>opera</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="optin-monster"><i class="fa fa-optin-monster" aria-hidden="true"></i> <span class="sr-only">Example of </span>optin-monster</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="pagelines"><i class="fa fa-pagelines" aria-hidden="true"></i> <span class="sr-only">Example of </span>pagelines</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="paypal"><i class="fa fa-paypal" aria-hidden="true"></i> <span class="sr-only">Example of </span>paypal</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="pied-piper"><i class="fa fa-pied-piper" aria-hidden="true"></i> <span class="sr-only">Example of </span>pied-piper</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="pied-piper-alt"><i class="fa fa-pied-piper-alt" aria-hidden="true"></i> <span class="sr-only">Example of </span>pied-piper-alt</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="pied-piper-pp"><i class="fa fa-pied-piper-pp" aria-hidden="true"></i> <span class="sr-only">Example of </span>pied-piper-pp</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="pinterest"><i class="fa fa-pinterest" aria-hidden="true"></i> <span class="sr-only">Example of </span>pinterest</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="pinterest-p"><i class="fa fa-pinterest-p" aria-hidden="true"></i> <span class="sr-only">Example of </span>pinterest-p</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="pinterest-square"><i class="fa fa-pinterest-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>pinterest-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="product-hunt"><i class="fa fa-product-hunt" aria-hidden="true"></i> <span class="sr-only">Example of </span>product-hunt</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="qq"><i class="fa fa-qq" aria-hidden="true"></i> <span class="sr-only">Example of </span>qq</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="quora"><i class="fa fa-quora" aria-hidden="true"></i> <span class="sr-only">Example of </span>quora</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="rebel"><i class="fa fa-ra" aria-hidden="true"></i> <span class="sr-only">Example of </span>ra <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="ravelry"><i class="fa fa-ravelry" aria-hidden="true"></i> <span class="sr-only">Example of </span>ravelry</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="rebel"><i class="fa fa-rebel" aria-hidden="true"></i> <span class="sr-only">Example of </span>rebel</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="reddit"><i class="fa fa-reddit" aria-hidden="true"></i> <span class="sr-only">Example of </span>reddit</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="reddit-alien"><i class="fa fa-reddit-alien" aria-hidden="true"></i> <span class="sr-only">Example of </span>reddit-alien</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="reddit-square"><i class="fa fa-reddit-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>reddit-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="renren"><i class="fa fa-renren" aria-hidden="true"></i> <span class="sr-only">Example of </span>renren</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="rebel"><i class="fa fa-resistance" aria-hidden="true"></i> <span class="sr-only">Example of </span>resistance <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="safari"><i class="fa fa-safari" aria-hidden="true"></i> <span class="sr-only">Example of </span>safari</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="scribd"><i class="fa fa-scribd" aria-hidden="true"></i> <span class="sr-only">Example of </span>scribd</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="sellsy"><i class="fa fa-sellsy" aria-hidden="true"></i> <span class="sr-only">Example of </span>sellsy</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="share-alt"><i class="fa fa-share-alt" aria-hidden="true"></i> <span class="sr-only">Example of </span>share-alt</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="share-alt-square"><i class="fa fa-share-alt-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>share-alt-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="shirtsinbulk"><i class="fa fa-shirtsinbulk" aria-hidden="true"></i> <span class="sr-only">Example of </span>shirtsinbulk</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="simplybuilt"><i class="fa fa-simplybuilt" aria-hidden="true"></i> <span class="sr-only">Example of </span>simplybuilt</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="skyatlas"><i class="fa fa-skyatlas" aria-hidden="true"></i> <span class="sr-only">Example of </span>skyatlas</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="skype"><i class="fa fa-skype" aria-hidden="true"></i> <span class="sr-only">Example of </span>skype</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="slack"><i class="fa fa-slack" aria-hidden="true"></i> <span class="sr-only">Example of </span>slack</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="slideshare"><i class="fa fa-slideshare" aria-hidden="true"></i> <span class="sr-only">Example of </span>slideshare</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="snapchat"><i class="fa fa-snapchat" aria-hidden="true"></i> <span class="sr-only">Example of </span>snapchat</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="snapchat-ghost"><i class="fa fa-snapchat-ghost" aria-hidden="true"></i> <span class="sr-only">Example of </span>snapchat-ghost</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="snapchat-square"><i class="fa fa-snapchat-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>snapchat-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="soundcloud"><i class="fa fa-soundcloud" aria-hidden="true"></i> <span class="sr-only">Example of </span>soundcloud</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="spotify"><i class="fa fa-spotify" aria-hidden="true"></i> <span class="sr-only">Example of </span>spotify</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="stack-exchange"><i class="fa fa-stack-exchange" aria-hidden="true"></i> <span class="sr-only">Example of </span>stack-exchange</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="stack-overflow"><i class="fa fa-stack-overflow" aria-hidden="true"></i> <span class="sr-only">Example of </span>stack-overflow</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="steam"><i class="fa fa-steam" aria-hidden="true"></i> <span class="sr-only">Example of </span>steam</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="steam-square"><i class="fa fa-steam-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>steam-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="stumbleupon"><i class="fa fa-stumbleupon" aria-hidden="true"></i> <span class="sr-only">Example of </span>stumbleupon</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="stumbleupon-circle"><i class="fa fa-stumbleupon-circle" aria-hidden="true"></i> <span class="sr-only">Example of </span>stumbleupon-circle</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="superpowers"><i class="fa fa-superpowers" aria-hidden="true"></i> <span class="sr-only">Example of </span>superpowers</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="telegram"><i class="fa fa-telegram" aria-hidden="true"></i> <span class="sr-only">Example of </span>telegram</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="tencent-weibo"><i class="fa fa-tencent-weibo" aria-hidden="true"></i> <span class="sr-only">Example of </span>tencent-weibo</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="themeisle"><i class="fa fa-themeisle" aria-hidden="true"></i> <span class="sr-only">Example of </span>themeisle</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="trello"><i class="fa fa-trello" aria-hidden="true"></i> <span class="sr-only">Example of </span>trello</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="tripadvisor"><i class="fa fa-tripadvisor" aria-hidden="true"></i> <span class="sr-only">Example of </span>tripadvisor</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="tumblr"><i class="fa fa-tumblr" aria-hidden="true"></i> <span class="sr-only">Example of </span>tumblr</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="tumblr-square"><i class="fa fa-tumblr-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>tumblr-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="twitch"><i class="fa fa-twitch" aria-hidden="true"></i> <span class="sr-only">Example of </span>twitch</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="twitter"><i class="fa fa-twitter" aria-hidden="true"></i> <span class="sr-only">Example of </span>twitter</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="twitter-square"><i class="fa fa-twitter-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>twitter-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="usb"><i class="fa fa-usb" aria-hidden="true"></i> <span class="sr-only">Example of </span>usb</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="viacoin"><i class="fa fa-viacoin" aria-hidden="true"></i> <span class="sr-only">Example of </span>viacoin</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="viadeo"><i class="fa fa-viadeo" aria-hidden="true"></i> <span class="sr-only">Example of </span>viadeo</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="viadeo-square"><i class="fa fa-viadeo-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>viadeo-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="vimeo"><i class="fa fa-vimeo" aria-hidden="true"></i> <span class="sr-only">Example of </span>vimeo</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="vimeo-square"><i class="fa fa-vimeo-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>vimeo-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="vine"><i class="fa fa-vine" aria-hidden="true"></i> <span class="sr-only">Example of </span>vine</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="vk"><i class="fa fa-vk" aria-hidden="true"></i> <span class="sr-only">Example of </span>vk</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="weixin"><i class="fa fa-wechat" aria-hidden="true"></i> <span class="sr-only">Example of </span>wechat <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="weibo"><i class="fa fa-weibo" aria-hidden="true"></i> <span class="sr-only">Example of </span>weibo</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="weixin"><i class="fa fa-weixin" aria-hidden="true"></i> <span class="sr-only">Example of </span>weixin</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="whatsapp"><i class="fa fa-whatsapp" aria-hidden="true"></i> <span class="sr-only">Example of </span>whatsapp</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="wikipedia-w"><i class="fa fa-wikipedia-w" aria-hidden="true"></i> <span class="sr-only">Example of </span>wikipedia-w</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="windows"><i class="fa fa-windows" aria-hidden="true"></i> <span class="sr-only">Example of </span>windows</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="wordpress"><i class="fa fa-wordpress" aria-hidden="true"></i> <span class="sr-only">Example of </span>wordpress</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="wpbeginner"><i class="fa fa-wpbeginner" aria-hidden="true"></i> <span class="sr-only">Example of </span>wpbeginner</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="wpexplorer"><i class="fa fa-wpexplorer" aria-hidden="true"></i> <span class="sr-only">Example of </span>wpexplorer</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="wpforms"><i class="fa fa-wpforms" aria-hidden="true"></i> <span class="sr-only">Example of </span>wpforms</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="xing"><i class="fa fa-xing" aria-hidden="true"></i> <span class="sr-only">Example of </span>xing</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="xing-square"><i class="fa fa-xing-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>xing-square</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="y-combinator"><i class="fa fa-y-combinator" aria-hidden="true"></i> <span class="sr-only">Example of </span>y-combinator</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="hacker-news"><i class="fa fa-y-combinator-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>y-combinator-square <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="yahoo"><i class="fa fa-yahoo" aria-hidden="true"></i> <span class="sr-only">Example of </span>yahoo</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="y-combinator"><i class="fa fa-yc" aria-hidden="true"></i> <span class="sr-only">Example of </span>yc <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="hacker-news"><i class="fa fa-yc-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>yc-square <span class="text-muted">(alias)</span></a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="yelp"><i class="fa fa-yelp" aria-hidden="true"></i> <span class="sr-only">Example of </span>yelp</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="yoast"><i class="fa fa-yoast" aria-hidden="true"></i> <span class="sr-only">Example of </span>yoast</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="youtube"><i class="fa fa-youtube" aria-hidden="true"></i> <span class="sr-only">Example of </span>youtube</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="youtube-play"><i class="fa fa-youtube-play" aria-hidden="true"></i> <span class="sr-only">Example of </span>youtube-play</a></div>

                <div class="fa-hover col-sm-1"><a data-font-icon="youtube-square"><i class="fa fa-youtube-square" aria-hidden="true"></i> <span class="sr-only">Example of </span>youtube-square</a></div>

            </div>
        </section>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#cms").siblings('a').attr('aria-expanded','true');
    $("ul#cms").addClass("show");
    $("ul#cms #cms-social-menu").addClass("active");

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var icon_list = $('#icons section').clone();

    $(document).on("click", ".icon", function() {
        $(this).parent().parent().find(".card-body").html(icon_list);
    });

    $(document).on("click", ".fa-hover a", function() {
        var icon = $(this).data('font-icon');
        $(this).closest('.collapse').parent().find(".icon").val('fa fa-'+icon);
        $(this).closest('.collapse').removeClass('show');
    });

    $('.edit-btn').on('click', function(){
        $("#editModal input[name='icon']").val($(this).data('font'));
        $("#editModal input[name='name']").val($(this).data('name'));
        $("#editModal input[name='link']").val($(this).data('link'));
        $("#editModal input[name='social_id']").val($(this).data('id'));
    });
    $('#custom-btn').on('click', function(){
        var count = $('#custom-field hr').length + 1;
        htmlText = '<div class="row"><div class="col-sm-3"><input type="text" name="icon[]" class="form-control icon" value="" data-toggle="collapse" href="#icon_collapse'+count+'" aria-expanded="false" aria-controls="icon_collapse'+count+'"/></div><div class="col-sm-4"><input type="text" name="name[]" class="form-control" placeholder="{{__('db.name')}}"/></div><div class="col-sm-4"><input type="text" name="link[]" class="form-control" placeholder="{{__('db.Link')}}"/></div><div class="col-sm-1"><span class="input-group-btn delete-fields"><button class="btn btn-danger btn-lg delete-btn" type="button">X</button></span></div><div class="collapse icon_collapse" id="icon_collapse'+count+'"><div class="card"><div class="card-body"></div></div></div></div><hr>';
        $('#custom-field').append(htmlText);
    });

    $(document).on("click", ".delete-btn", function() {
        $(this).parent().parent().parent().next().remove();
        $(this).closest(".row").remove();
    });

    $('#social-table').DataTable( {
        "order": [],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 2]
            },
            {
                'checkboxes': {
                   'selectRow': true
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi', selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: 'PDF',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
                // customize: function(doc) {
                //     for (var i = 1; i < doc.content[1].table.body.length; i++) {
                //         if (doc.content[1].table.body[i][0].text.indexOf('<img src=') !== -1) {
                //             var imagehtml = doc.content[1].table.body[i][0].text;
                //             var regex = /<img.*?src=['"](.*?)['"]/;
                //             var src = regex.exec(imagehtml)[1];
                //             var tempImage = new Image();
                //             tempImage.src = src;
                //             var canvas = document.createElement("canvas");
                //             canvas.width = tempImage.width;
                //             canvas.height = tempImage.height;
                //             var ctx = canvas.getContext("2d");
                //             ctx.drawImage(tempImage, 0, 0);
                //             var imagedata = canvas.toDataURL("image/png");
                //             delete doc.content[1].table.body[i][0].text;
                //             doc.content[1].table.body[i][0].image = imagedata;
                //             doc.content[1].table.body[i][0].fit = [30, 30];
                //         }
                //     }
                // },
            },
            {
                extend: 'csv',
                text: 'CSV',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible',
                    format: {
                        body: function ( data, row, column, node ) {
                            if (column === 0 && (data.indexOf('<img src=') !== -1)) {
                                var regex = /<img.*?src=['"](.*?)['"]/;
                                data = regex.exec(data)[1];
                            }
                            return data;
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                }
            },
            {
                extend: 'colvis',
                text: 'Column visibility',
                columns: ':gt(0)'
            },
        ],
    } );

    $( "#tablecontents" ).sortable({
      items: "tr",
      cursor: 'move',
      opacity: 0.6,
      update: function() {
          sendOrderToServer();
      }
    });

    function sendOrderToServer() {
        var order = [];
        $('tr.row1').each(function(index,element) {
          order.push({
            id: $(this).attr('data-id'),
            position: index+1
          });
        });

        $.ajax({
          type: "POST",
          url: "{{ url('superadmin/social-section/sort') }}",
          data: {
            order:order
          },
          success: function(response) {
              alert(response);
          }
        });

    }
</script>
@endpush
