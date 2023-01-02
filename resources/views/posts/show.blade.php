
@extends('layouts.app')

@section('content')
         
        <!--================Breadcrumb Area =================-->
        <section class="breadcrumb_area blog_banner_two">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background=""></div>
            <div class="container">
                <div class="page-cover text-center">
                    <h2 class="page-cover-tittle f_48">Blog Details page</h2>
                    <ol class="breadcrumb">
                        <li><a href="/maindir/index.html">Home</a></li>
                        <li><a href="/maindir/blog.html">Blog</a></li>
                        <li class="active">Blog Details</li>
                    </ol>
                </div>
            </div>
        </section>
        <!--================Breadcrumb Area =================-->
        
        <!--================Blog Area =================-->
        <section class="blog_area single-post-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 posts-list">
                        <div class="single-post row">
                            <div class="col-lg-12">
 
                            @if(!Auth::guest())
                            
                                @if(Auth()->user()->id == $post->user_id)
                                <div id="edit_del">
                                    <h2 class="blog_title">{{$post->title}}</h2>
                        
                                    <form action="{{ action('PostsController@update', $post->id) }}" method="POST" class="float-right">

                                        <a href="/posts/{{$post->id}}/edit" class="btn btn-primary float-right"><i class="fa fa-edit"></i></a>

                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" name="sub_action" value="del" class="btn btn-danger" onclick="confirm('{{ __('Are you sure you want to delete this item?') }}') ? this.parentElement.submit() : ''">
                                            <i class="fa fa-close"></i>
                                        </button>
                                         
                                <button type="submit" name="sub_action" value="del" class="btn-danger btn-link action_icons red" id="action_icons"><i class="fa fa-close"></i></button>
                                        <!--textarea class="form-control" id="article-ckeditor" name="body" placeholder="Body/Text" rows="5"></textarea-->
                                    </form>
                                </div>
                                @endif
                            @endif

                            <div class="feature-img">
                                @include('inc.messages')
                                <img class="img-fluid" src="/storage/cover_imgs/{{$post->cover_img}}" alt="">
                            </div>									
                            </div>
                            <div class="col-lg-3  col-md-3">
                                <div class="blog_info text-right">
                                    <div class="post_tag">
                                        <a href="/maindir/#">Food,</a>
                                        <a href="/maindir/#">Technology,</a>
                                        <a href="/maindir/#">Politics,</a>
                                        <a href="/maindir/#">Lifestyle</a>
                                    </div>
                                    <ul class="blog_meta list_style">
                                        <li><a href="/maindir/#">{{$post->user->name}}<i class="lnr lnr-user"></i></a></li>
                                        <li><a href="/maindir/#">{{$post->created_at}}<i class="lnr lnr-calendar-full"></i></a></li>
                                        <li><a href="/maindir/#">1.2M Views<i class="lnr lnr-eye"></i></a></li>
                                        <li><a href="/maindir/#">{{count($post->comments)}} Comments<i class="lnr lnr-bubble"></i></a></li>
                                    </ul>
                                    <ul class="social-links">
                                        <li><a href="/maindir/#"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="/maindir/#"><i class="fa fa-twitter"></i></a></li>
                                        <li><a href="/maindir/#"><i class="fa fa-github"></i></a></li>
                                        <li><a href="/maindir/#"><i class="fa fa-behance"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-9 blog_details">
                                

                                <p class="excert">{{$post->body}}
                                    Boot camps have its supporters and its detractors. Some people do not understand why you should have to spend money on boot camp when you can get the MCSE study materials yourself at a fraction of the camp price. However, who has the willpower to actually sit through a self-imposed MCSE training. who has the willpower to actually sit through a self-imposed
                                </p>
                                <p>
                                    Boot camps have its supporters and its detractors. Some people do not understand why you should have to spend money on boot camp when you can get the MCSE study materials yourself at a fraction of the camp price. However, who has the willpower to actually sit through a self-imposed MCSE training. who has the willpower to actually sit through a self-imposed
                                </p>
                            </div>
                            <div class="col-lg-12">
                            <div class="quotes">Addition image(s) topic or short notes --> {{$post->title2}}</div>
                                <div class="row">
                                    <div class="col-6">
                                        <img class="img-fluid add_img" src="/storage/cover_imgs/{{$post->cover_img2}}" alt="">
                                    </div>
                                    <div class="col-6">
                                        <img class="img-fluid add_img" src="/storage/cover_imgs/{{$post->cover_img3}}" alt="">
                                    </div>	
                                    <div class="col-lg-12 mt-25">
                                        <p>
                                            {{$post->body2}} Additional image short note boot camps have its supporters and its detractors. Some people do not understand why you should have to spend money on boot camp when you can get the MCSE study materials yourself at a fraction of the camp price. However, who has the willpower.
                                        </p>
                                        <p>
                                            Additional image short note boot camps have its supporters and its detractors. Some people do not understand why you should have to spend money on boot camp when you can get the MCSE study materials yourself at a fraction of the camp price. However, who has the willpower.
                                        </p>											
                                    </div>									
                                </div>
                            </div>
                        </div>
                        <div class="navigation-area">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-12 nav-left flex-row d-flex justify-content-start align-items-center">
                                    <div class="thumb">
                                        <a href="/maindir/#"><img class="img-fluid" src="/maindir/image/blog/prev.jpg" alt=""></a>
                                    </div>
                                    <div class="arrow">
                                        <a href="/maindir/#"><span class="lnr text-white lnr-arrow-left"></span></a>
                                    </div>
                                    <div class="detials">
                                        <p>Prev Post</p>
                                        <a href="/maindir/#"><h4>Space The Final Frontier</h4></a>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12 nav-right flex-row d-flex justify-content-end align-items-center">
                                    <div class="detials">
                                        <p>Next Post</p>
                                        <a href="/maindir/#"><h4>Telescopes 101</h4></a>
                                    </div>
                                    <div class="arrow">
                                        <a href="/maindir/#"><span class="lnr text-white lnr-arrow-right"></span></a>
                                    </div>
                                    <div class="thumb">
                                        <a href="/maindir/#"><img class="img-fluid" src="/maindir/image/blog/next.jpg" alt=""></a>
                                    </div>										
                                </div>									
                            </div>
                        </div>
                        <div class="comments-area">
                
                            

                            
                        @foreach ($comments as $comment)
                            <div class="comment-list">
                                <div class="single-comment justify-content-between d-flex">
                                    <div class="user justify-content-between d-flex">
                                        <div class="thumb">
                                            <img class="comment_img" src="/maindir/image/blog/c1.jpg" alt="">
                                        </div>
                                        <div class="row comment_desc">
                                            <div class="col">
                                                <p class="comment"><b class="com_bold">{{ucfirst($comment->cname)}}</b> {{$comment->cbody}} Never say goodbye till the end comes!</p>
                                            </div> 
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="comment_date_cont">
                                    <div class="comment_date">
                                        <small class="date">{{date('M-d-Y', strtotime($comment->created_at))}} at {{date('H:i', strtotime($comment->created_at))}}</small>
                                    </div> 
                                    <div class="reply-btn">
                                        <button class="btn-reply text-uppercase" type="submit" onclick="{{$comment->id}}()"><i class="fa fa-reply"></i></button> 
                                    </div>  
                                </div>
                                <div id="rep" class="rep_rep">
                                    <form>
                                        <input class="rep1" type="text" name="rep_name" placeholder="Name">
                                        <input id="txtInput" class="rep2" type="text" name="rep_comment" placeholder="Write a reply..." required>
                                        <button class="rep_submit" type="submit" name="rep_submit">&nbsp;<i class="fa fa-send"></i>&nbsp;&nbsp;</button>
                                    </form>
                                    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js" type="text/javascript"></script> 
                                    <script>

                                        var x = document.getElementById("rep");
                                            x.style.display = "none";

                                        var data = {!! $comment->id !!};
                                        var data2 = data + ();

                                        function $data {
                                            var x = document.getElementById("rep");
                                            if (x.style.display === "none") {
                                                x.style.display = "block";
                                                /**document.getElementById('myP').style.marginTop = '50px';
                                                var tray_bar = document.getElementById("comment-list");
                                                var pixelMargin = parseInt(tray_bar.style.marginBottom, 50);
                                                pixelMargin += num;
                                                tray_bar.style.marginBottom = pixelMargin.toString() + 'px';
                                                **/
                                            } else {
                                                x.style.display = "none";
                                            }
                                        }

                                        $("#txtInput").height("100px");
                                        $("#txtInput").autogrow();
                                    </script>
                                </div>
                            </div>	
                        @endforeach	
                          				
                        </div>

                        


                        <div class="comment-form">
                            <h4>Leave a Reply</h4>
                            <form action="{{action('PostsController@store')}}" method="POST">
                                @csrf
                                <div class="form-group form-inline">
                                  <div class="form-group col-lg-6 col-md-6 name">
                                  <input type="hidden" name="pass_id" value="{{$post->id}}">
                                    <input type="text" class="form-control" id="name" name="cname" placeholder="Enter Name" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Name'" required>
                                  </div>										
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control mb-10" rows="5" name="cbody" placeholder="Messege" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Messege'" required></textarea>
                                </div>
                                <button type="submit" class="view_btn button_hover" name="store_action" value="sub_com">Post Comment</button>
                                <!--a href="/maindir/#" class="view_btn button_hover">Post Comment</a-->	
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="blog_right_sidebar">
                            <aside class="single_sidebar_widget search_widget">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search Posts">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button"><i class="lnr lnr-magnifier"></i></button>
                                    </span>
                                </div><!-- /input-group -->
                                <div class="br"></div>
                            </aside>
                            <aside class="single_sidebar_widget author_widget">
                                <img class="author_img rounded-circle" src="/maindir/image/blog/author.png" alt="">
                                <h4>Charlie Barber</h4>
                                <p>Senior blog writer</p>
                                <div class="social_icon">
                                    <a href="/maindir/#"><i class="fa fa-facebook"></i></a>
                                    <a href="/maindir/#"><i class="fa fa-twitter"></i></a>
                                    <a href="/maindir/#"><i class="fa fa-github"></i></a>
                                    <a href="/maindir/#"><i class="fa fa-behance"></i></a>
                                </div>
                                <p>Boot camps have its supporters andit sdetractors. Some people do not understand why you should have to spend money on boot camp when you can get. Boot camps have itssuppor ters andits detractors.</p>
                                <div class="br"></div>
                            </aside>
                            <aside class="single_sidebar_widget popular_post_widget">
                                <h3 class="widget_title">Popular Posts</h3>
                                <div class="media post_item">
                                    <img src="/maindir/image/blog/post1.jpg" alt="post">
                                    <div class="media-body">
                                        <a href="/maindir/blog-details.html"><h3>Space The Final Frontier</h3></a>
                                        <p>02 Hours ago</p>
                                    </div>
                                </div>
                                <div class="media post_item">
                                    <img src="/maindir/image/blog/post2.jpg" alt="post">
                                    <div class="media-body">
                                        <a href="/maindir/blog-details.html"><h3>The Amazing Hubble</h3></a>
                                        <p>02 Hours ago</p>
                                    </div>
                                </div>
                                <div class="media post_item">
                                    <img src="/maindir/image/blog/post3.jpg" alt="post">
                                    <div class="media-body">
                                        <a href="/maindir/blog-details.html"><h3>Astronomy Or Astrology</h3></a>
                                        <p>03 Hours ago</p>
                                    </div>
                                </div>
                                <div class="media post_item">
                                    <img src="/maindir/image/blog/post4.jpg" alt="post">
                                    <div class="media-body">
                                        <a href="/maindir/blog-details.html"><h3>Asteroids telescope</h3></a>
                                        <p>01 Hours ago</p>
                                    </div>
                                </div>
                                <div class="br"></div>
                            </aside>
                            <aside class="single_sidebar_widget ads_widget">
                                <a href="/maindir/#"><img class="img-fluid" src="/maindir/image/blog/add.jpg" alt=""></a>
                                <div class="br"></div>
                            </aside>
                            <aside class="single_sidebar_widget post_category_widget">
                                <h4 class="widget_title">Post Catgories</h4>
                                <ul class="list_style cat-list">
                                    <li>
                                        <a href="/maindir/#" class="d-flex justify-content-between">
                                            <p>Technology</p>
                                            <p>37</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/maindir/#" class="d-flex justify-content-between">
                                            <p>Lifestyle</p>
                                            <p>24</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/maindir/#" class="d-flex justify-content-between">
                                            <p>Fashion</p>
                                            <p>59</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/maindir/#" class="d-flex justify-content-between">
                                            <p>Art</p>
                                            <p>29</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/maindir/#" class="d-flex justify-content-between">
                                            <p>Food</p>
                                            <p>15</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/maindir/#" class="d-flex justify-content-between">
                                            <p>Architecture</p>
                                            <p>09</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/maindir/#" class="d-flex justify-content-between">
                                            <p>Adventure</p>
                                            <p>44</p>
                                        </a>
                                    </li>															
                                </ul>
                                <div class="br"></div>
                            </aside>
                            <aside class="single-sidebar-widget newsletter_widget">
                                <h4 class="widget_title">Newsletter</h4>
                                <p>
                                Here, I focus on a range of items and features that we use in life without
                                giving them a second thought.
                                </p>
                                <div class="form-group d-flex flex-row">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                        </div>
                                        <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Enter email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter email'">
                                    </div>
                                    <a href="/maindir/#" class="bbtns">Subcribe</a>
                                </div>	
                                <p class="text-bottom">You can unsubscribe at any time</p>	
                                <div class="br"></div>							
                            </aside>
                            <aside class="single-sidebar-widget tag_cloud_widget">
                                <h4 class="widget_title">Tag Clouds</h4>
                                <ul class="list_style">
                                    <li><a href="/maindir/#">Technology</a></li>
                                    <li><a href="/maindir/#">Fashion</a></li>
                                    <li><a href="/maindir/#">Architecture</a></li>
                                    <li><a href="/maindir/#">Fashion</a></li>
                                    <li><a href="/maindir/#">Food</a></li>
                                    <li><a href="/maindir/#">Technology</a></li>
                                    <li><a href="/maindir/#">Lifestyle</a></li>
                                    <li><a href="/maindir/#">Art</a></li>
                                    <li><a href="/maindir/#">Adventure</a></li>
                                    <li><a href="/maindir/#">Food</a></li>
                                    <li><a href="/maindir/#">Lifestyle</a></li>
                                    <li><a href="/maindir/#">Adventure</a></li>
                                </ul>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================Blog Area =================-->
        
@endsection
        
        