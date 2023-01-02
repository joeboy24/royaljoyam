@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-xl-8 offset-xl-2">
        <a href="/posts" class="btn btn-default" role="button"><< Posts</a>
        <h1>Create Post</h1>
        
        <form action="{{action('PostsController@store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('inc.messages')
            <label>Title</label>
            <input type="text" class="form-control" name="title" placeholder="Tittle" required/>
            <label>Body</label>
            <textarea name="body" id="article-ckeditorr" class="form-control" placeholder="Body/Text" rows="5" required></textarea>
            <br>
            <input type="file" name="cover_img" required><br>
            <label>Additional Image Title</label>
            <input type="text" class="form-control" name="title2" placeholder="Tittle" />
            <label>Additional Body Text</label>
            <textarea name="body2" id="article-ckeditorr" class="form-control" placeholder="Body/Text" rows="5"></textarea>
            <br>
            <label>Image 2</label>
            <input type="file" name="cover_img2">
            <br>
            <label>Image 3</label>
            <input type="file" name="cover_img3">
            <br><br>
            <button type="submit" class="btn btn-primary" name="store_action" value="create_post">Submit</button>
            <button type="submit" class="btn btn-primary" name="store_action" value="sub_com">Submit222</button>

            <button type="submit" class="view_btn button_hover" name="store_action" value="sub_com">Post Comment</button>
        </form>
    </div>
</div>
    
@endsection