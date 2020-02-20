@extends('layouts.app')

@include('partials.ckeditor')

@section('content')

    <h1 class="pt-3">Création d'un article</h1>

    <form action="{{ route('admin.posts.store') }}" method="post" enctype="multipart/form-data">
        @csrf


        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text"
                   class="form-control" name="title" id="title" placeholder="Titre de l'article"
                   value="{{ old('title') }}">
            @error('title') <small id="title" class="form-text text-danger">{{$message}}</small> @enderror
        </div>

        <div class="form-group">
            <label for="categories">Catégories</label>
            <select class="form-control" name="categories[]" id="categories" multiple>

                @foreach($categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                
            </select>
        </div>

        <div class="form-group">
            <label for="content">Contenue</label>
            <textarea class="form-control " name="content" id="editor" rows="3" placeholder="Contenue de l'article..">
            {{ old('content') }}
        </textarea>
            @error('content') <small id="content" class="form-text text-danger">{{$message}}</small> @enderror
        </div>

        <div class="form-group">
            <label for="excerpt">Extrait</label>
            <textarea class="form-control " name="excerpt" rows="3"
                      placeholder="Court présentation de l'article..">{{ old('excerpt') }}</textarea>
            @error('excerpt') <small id="content" class="form-text text-danger">{{$message}}</small> @enderror
        </div>

        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" class="form-control-file files" name="image" id="image" value="{{ old('image') }} "
                   placeholder="Image de présentation de l'article">
            @error('image') <small id="image" class="form-text text-danger">{{$message}}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>

    </form>
@endsection

