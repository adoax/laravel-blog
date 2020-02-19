@extends('layouts.app')


@section('content')
    <div class="row">
        @foreach($posts as $post)
            <div class="col-6">
                <div class="card">
                    <a href="{{ route('admin.posts.show', $post->id) }}">
                        <img class="card-img-top" src="{{asset('storage/images/' . $post->image)}}" alt="">
                    </a>
                    <div class="card-body">
                        <a href="{{ route('admin.posts.show', $post->id) }}" class="link-url-card">
                            <h4 class="card-title">{{$post->title}}</h4>
                        </a>
                        <p class="card-text">{{$post->excerpt}}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection