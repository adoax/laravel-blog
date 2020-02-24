@extends('layouts.app')

@section('content')

    <a href="{{ route('admin.categories.create') }}" class="btn btn-success mb-3 float-right">Créer une category</a>
    <table class="table table-striped">
        <thead class="thead-inverse">
        <tr>
            <th>Titre</th>
            <th>Nombre utilisée</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->name }}</td>
            <td>{{ $category->posts ? count($category->posts) : '0' }}</td>
            <td class="row">
                <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-success">Voir</a>
                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">Editer</a>
                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
            @endforeach
        </tbody>
    </table>

@endsection