@extends('common.layout')

@section('title', 'Login')

@section('content')
    <div class="row">
        <div class="mx-auto" style="width: 700px;">
            <div class="jumbotron jumbotron-fluid">
                <h1 class="display-4">Flick it 😉</h1>
                <p class="lead">I'll come and show you your galleries in Flickr.</p>
            </div>
        </div>
    </div>
    <div class="row justify-content-md-center">
        <a class="btn btn-primary col-md-3" href="{{ route('auth.redirect') }}" role="button">Try me!</a>
    </div>
@endsection
