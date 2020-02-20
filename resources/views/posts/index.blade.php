@extends('layouts.app')

{{--<div class="background-img-top"></div>--}}

@section('content')
    <div class="row">
        @foreach($posts as $post)
            <div class="col-6">
                <div class="card">
                    <a href="{{ route('posts.show', $post->id) }}">
                        <img class="card-img-top" src="{{asset('storage/images/thumbs/' . $post->image)}}" alt="">
                    </a>
                    <div class="card-body">
                        <a href="{{ route('posts.show', $post->id) }}" class="link-url-card">
                            <h2 class="card-title">{{$post->title}}</h2>
                        </a>
                        <p class="card-text">{{$post->excerpt}}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection