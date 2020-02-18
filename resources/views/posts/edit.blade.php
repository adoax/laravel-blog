@extends('layouts.app')

@include('partials.ckeditor')

@section('content')

    <h1 class="pt-3">Edition de {{ $post->title }}</h1>

    <form action="{{ route('posts.update', $post->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text"
                   class="form-control" name="title" id="title" placeholder="Titre de l'article"
                   value="{{ old('title', $post->title) }}">
            @error('title') <small id="title" class="form-text text-danger">{{$message}}</small> @enderror
        </div>

        <div class="form-group">
            <label for="content">Contenue</label>
            <textarea class="form-control" name="content" id="editor" rows="3" placeholder="Contenue de l'article..">{{ old('content', $post->content) }}
        </textarea>
            @error('content') <small id="content" class="form-text text-danger">{{$message}}</small> @enderror
        </div>

        <div class="form-group">
            <label for="excerpt">Extrait</label>
            <textarea class="form-control " name="excerpt" rows="3" placeholder="Court présentation de l'article..">{{ old('excerpt', $post->excerpt) }}
        </textarea>
            @error('excerpt') <small id="content" class="form-text text-danger">{{$message}}</small> @enderror
        </div>


        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" class="form-control-file files" name="image" id="image"
                   value="{{ old('image', $post->image) }}" placeholder="Image de présentation de l'article">
            @error('image') <small id="image" class="form-text text-danger">{{$message}}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>

    </form>
@endsection