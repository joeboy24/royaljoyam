@extends('layouts.app')

@section('content')
<a href="/posts" class="btn btn-default">Go back</a>
<h1>{{$post->title}}</h1>
<div>
    <img width="100%" height="400" src="/storage/cover_imgs/{{$post->cover_img}}">
<div>
<br><br>
<div class="jumbotron">
    <h3>{!!$post->body!!}</h3>
    <small>Written on {{$post->created_at}}</small><br>
    <small>By {{$post->user->name}}</small><br>

    @if(!Auth::guest())
        @if(Auth()->user()->id == $post->user_id)
            <a href="/posts/{{$post->id}}/edit" class="btn btn-primary">Edit</a>

            <form action="{{ action('PostsController@destroy', $post->id) }}" method="POST" class="float-right">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="submit" value="Delete" class="btn btn-danger">
                <textarea class="form-control" id="article-ckeditor" name="body" placeholder="Body/Text" rows="5"></textarea>
            </form>
        @endif
    @endif

    <!--form action="{{ action('PostsController@dltup', $post->id) }}" method="POST" class="float-right">
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" value="Delete" class="btn btn-danger">
    </form-->

</div>
    
@endsection