@extends('layouts.app')

@section('content')
    <h1 class="pt-3">{{ $post->title }}</h1>
    <img class="img-fluid" src="{{ asset('storage/images/'. $post->image ) }}" alt="">
    {!! $post->content !!}
@endsection
