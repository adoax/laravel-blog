@extends('layouts.app')

@section('content')

    <a href="{{ route('posts.create') }}" class="btn btn-success mb-3 float-right">Créer un article</a>
    <table class="table table-striped">
        <thead class="thead-inverse">
        <tr>
            <th>Titre</th>
            <th>Extrait</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($posts as $post)
        <tr>
            <td>{{ $post->title }}</td>
            <td>{{ $post->excerpt }}</td>
            <td class="row">
                <a href="{{ route('posts.show', $post->id) }}" class="btn btn-success">Voir</a>
                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-primary">Editer</a>
                <form action="{{ route('posts.destroy', $post->id) }}" method="post">
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