@extends('layouts.app')

@section('content')
    <img class="img- rounded " src="{{ asset('storage/images/'. $post->image ) }}" alt="">
    <h1 class="pt-3">{{ $post->title }}</h1>
    {{$post->categories->pluck('name')->implode(', ')}}
    {!! $post->content !!}

    <hr>
    <h2 class="pt-3">Commentaire</h2>

    @foreach($comments as $comment)
        <h3>{{$comment->user_id}}</h3>
        <p>{{$comment->content}}</p>
    @endforeach

    @auth
        <form action="{{ route('comments.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="content">Commentaire</label>
                <textarea class="form-control" name="content" id="content" rows="3">{{old('comment')}}</textarea>

                @error('content') <small id="content" class="form-text text-muted">{{ $message }}</small> @enderror
            </div>
            <input type="hidden" name="post_id" id="post_id" value="{{$post->id}}">

            <button type="submit" class="btn btn-success">Enovyer</button>
        </form>
    @else
        <h3>Pour laisser une commentaire veuillez êtres <a href="{{ route('login') }}">Connectez</a></h3>
    @endauth


@endsection
