@extends('layouts.app')

@section('content')
    <img class="img- rounded " src="{{ asset('storage/images/'. $post->image ) }}" alt="">
    <h1 class="pt-3">{{ $post->title }}</h1>
    {{$post->categories->pluck('name')->implode(', ')}}
    {!! $post->content !!}
@endsection
