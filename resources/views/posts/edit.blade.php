@extends('layouts.dashlay')

@section('sidebar-wrapper')
  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item  ">
        <a class="nav-link" href="./dashboard.html">
          <i class="material-icons">dashboard</i>
          <p>Dashboard</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="/dashuser">
          <i class="material-icons">person</i>
          <p>User Profile</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/galleryview">
          <i class="material-icons">image</i>
          <p>Gallery</p>
        </a>
      <li class="nav-item active ">
        <a class="nav-link" href="/posts">
          <i class="material-icons">library_books</i>
          <p>Posts</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="./typography.html">
          <i class="material-icons">content_paste</i>
          <p>Typography</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="./icons.html">
          <i class="material-icons">bubble_chart</i>
          <p>Icons</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="./map.html">
          <i class="material-icons">location_ons</i>
          <p>Maps</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="./notifications.html">
          <i class="material-icons">notifications</i>
          <p>Notifications</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="./rtl.html">
          <i class="material-icons">language</i>
          <p>RTL Support</p>
        </a>
      </li>
      <li class="nav-item active-pro ">
        <a class="nav-link" href="./upgrade.html">
          <i class=""></i>
          <p>&nbsp;</p>
        </a>
      </li>
    </ul>
  </div>  
@endsection

@section('content')
<div class="content">
    <div class="container-fluid">     

      <div class="row">
        <div class="col-md-12">
          @include('inc.messages')
            <div class="card">
                <div class="card-header card-header-primary">
                    <h2 class="card-title ">EDIT POST</h2>
                    <p class="card-category"> Use this panel to edit post</p>
                </div>
                <div class="card-body">
                    
                            <div class="col-xl-8 offset-xl-2">
                                
                                <form action="{{ action('PostsController@update', $post->id) }}" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="_method" value="PUT">
                    
                                    @csrf
                                    <label>Title</label>
                                    <input type="text" value="{{$post->title}}" class="form-control" name="title" required/>
                                    <label>Body</label>
                                    <textarea class="form-control" id="article-ckeditor" name="body" rows="5" required>{{$post->body}}</textarea>
                                    <br>
                                    
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Choose Image Category:</label>
                                        <select name="cat" class="form-control" id="cat">
                                        @foreach ($pcats as $pcat)
                                            @if($pcat->name == $post->post_cat)
                                              <option selected>{{$pcat->name}}</option>
                                            @else
                                              <option>{{$pcat->name}}</option>
                                            @endif
                                        @endforeach
                                        </select>
                                    </div>
            
                                    <input type="file" class="upfiles" name="cover_img" value="{{$post->cover_img}}"><br>
                                    <input type="text" class="form-control" name="title2" value="{{$post->title2}}" placeholder="Additional Image Title"/>
                                    <textarea name="body2" id="article-ckeditorr" class="form-control" rows="5" placeholder="Additional Body Text">{{$post->body2}}</textarea>
                                    <br>
                                    <label>Image 2</label>
                                    <input type="file" class="upfiles" name="cover_img2">
                                    <br>
                                    <label>Image 3</label>
                                    <input type="file" class="upfiles" name="cover_img3">
                                    <br><br>
                                    <button type="submit" name="sub_action" class="btn btn-primary" value="edit_post"><i class="fa fa-save"></i> &nbsp; Update</button>
                                    
                                </form>
                    
                            </div>
                          
                       
                    
                    
                </div>
                </div>
            </div>
      </div>

    </div>
</div>

</div>
    
@endsection