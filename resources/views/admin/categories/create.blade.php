@extends('layouts.app')

@section('content')

    <form action="{{ route('admin.categories.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" name="name" id="name"
                   placeholder="Nom de la categorie" value="{{old('name')}}">
            @error('name') <small id="helpId" class="form-text text-muted">{{$message}}</small> @enderror
        </div>

        <button class="btn btn-success">Enregistrer</button>
    </form>

@endsection