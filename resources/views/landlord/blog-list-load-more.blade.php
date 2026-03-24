@foreach($blogs as $blog)
<div class="col-md-6 offset-md-3 mb-5">
    <a href="{{url('/blog')}}/{{$blog->slug}}">
        <div class="blog-item">
            <img src="{{asset('landlord/images/blog')}}/{{$blog->featured_image}}" alt="{{$blog->title}}"/>
            <h2 class="mt-3 text-center">{{$blog->title}}</h2>
        </div>
    </a>
</div>
@endforeach