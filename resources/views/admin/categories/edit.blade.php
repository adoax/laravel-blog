@extends('layouts.app')

@section('content')

    <form action="{{ route('admin.categories.update', $category->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" name="name" id="name"
                   placeholder="Nom de la categorie" value="{{old('name', $category->name)}}">
            @error('name') <small id="helpId" class="form-text text-muted">{{$message}}</small> @enderror
        </div>

        <button class="btn btn-success">Enregistrer</button>
    </form>

@endsection