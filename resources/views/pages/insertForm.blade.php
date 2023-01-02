@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-xl-8 offset-xl-2">
        <a href="/posts" class="btn btn-default" role="button"><< Posts</a>
        <h1>Trial</h1>
        
        <form action="/insert" method="POST">
            @csrf
            <label>Title</label>
            <input type="text" class="form-control" name="title" placeholder="Tittle" />
            <label>Body</label>
            <textarea class="form-control" name="body" placeholder="Body/Text" rows="5"></textarea>
            <input type="submit" class="btn btn-primary" name="submit" value="Submit" />
        </form>
    </div>
</div>
    
@endsection