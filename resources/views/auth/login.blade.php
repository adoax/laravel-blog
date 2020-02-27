@extends('layouts.app')

@section('content')
    <div class="container pt-5">
        <div class="row justify-content-center ">
            <div class="col-md-6">
                <div class="carousel-item active">
                    <img src="./images/header.jpg" class="img-fluid" alt="Image de formulaire de connexions">
                    <div class="carousel-caption d-none d-md-block">
                        <h3>Commensez des maitenants</h3>
                        <p class="text-white">Vous pouvez en créer en en quelque minute !</p>
                        <div class="btn btn-success"><a href="{{route('register')}}" class="text-white">Enregistrer vous</a> </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h3 class="text-center pt-2">Bienvenue !</h3>

                <form action="{{route('login')}}" method="POST">
                    @csrf
                    <div class="form-group ">
                        <label for="email">Adresse Mail</label>
                        <input type="email"
                               class="form-control ui-form-input  @error('email') is-invalid @enderror " name="email" id="email"
                               placeholder="john@doe.fr" value="{{ old('email') }}">
                        @error('email') <small id="helpId" class="form-text text-muted">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Votre mot de passe</label>
                        <input type="password"
                               class="form-control  ui-form-input @error('password') is-invalid @enderror"
                               name="password" id="password" placeholder="*****">
                        @error('password') <small id="helpId" class="form-text text-muted">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group row pt-3">
                        <div class="col-6">

                        <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input ui-form-check" name="remember" id="" value="checkedValue"
                                {{ old('remember') ? 'checked' : '' }}>
                            Se souvenir de moi
                        </label>
                    </div>
                        </div>
                        <div class="col-6">

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">
                                    {{ __('Mot de passe oublié ?') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-block mt-4">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
@endsection
