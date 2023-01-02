@extends('layouts.app')

@section('content')
    @guest
    @else
    <!--a href="/posts/create" class="btn btn-primary">Create Post</a-->
    @endguest

        <!--================ Banner Area =================-->
        <section class="banner_area blog_banner d_flex align-items-center">
            <div class="overlay bg-parallax" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background=""></div>
            <div class="container">
                <div class="banner_content text-center">
                    <h4>Dude You’re Getting <br />a Telescope</h4>
                    <p>There is a moment in the life of any aspiring astronomer that it is time to buy that first</p>
                    <a href="/register"><button class="view_btn button_hover">Register</button></a>
                </div>
            </div>
        </section>
        <!--================ Banner Area =================-->

        
        <!--================ Blog Category Area =================-->
        <section class="blog_categorie_area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="categories_post">
                            <img src="maindir/image/blog/cat-post/cat-post-3.jpg" alt="post">
                            <div class="categories_details">
                                <div class="categories_text">
                                    <a href="maindir/blog-details.html"><h5>Social Life</h5></a>
                                    <div class="border_line"></div>
                                    <p>Enjoy your social life together</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="categories_post">
                            <img src="maindir/image/blog/cat-post/cat-post-2.jpg" alt="post">
                            <div class="categories_details">
                                <div class="categories_text">
                                    <a href="maindir/blog-details.html"><h5>Politics</h5></a>
                                    <div class="border_line"></div>
                                    <p>Be a part of politics</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="categories_post">
                            <img src="maindir/image/blog/cat-post/cat-post-1.jpg" alt="post">
                            <div class="categories_details">
                                <div class="categories_text">
                                    <a href="maindir/blog-details.html"><h5>Food</h5></a>
                                    <div class="border_line"></div>
                                    <p>Let the food be finished</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Blog Category Area =================-->
        
        <!--================ Blog Area =================-->
        <section class="blog_area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="blog_left_sidebar">


                @if(count($posts) > 0)
                    @foreach ($posts as $post)
                        @if($post->del != 'yes')        
                            <article class="row blog_item">
                               <div class="col-md-3">
                 <!-------------Left note------------>
                                   <div class="blog_info text-right">
                                        <div class="post_tag">
                                            <a href="maindir/#">Food,</a>
                                            <a href="maindir/#">Technology,</a>
                                            <a href="maindir/#">Politics,</a>
                                            <a href="maindir/#">Lifestyle</a>
                                        </div>
                                        <ul class="blog_meta list_style">
                                            <li><a href="maindir/#">{{$post->user->name}}<i class="lnr lnr-user"></i></a></li>
                                            <li><a href="maindir/#">{{date('M-d-Y', strtotime($post->created_at))}}<i class="lnr lnr-calendar-full"></i></a></li>
                                            <li><a href="maindir/#">1.2M Views<i class="lnr lnr-eye"></i></a></li>
                                            <li><a href="maindir/#">{{count($post->comments)}} Comments<i class="lnr lnr-bubble"></i></a></li>
                                        </ul>
                                    </div>
                               </div>
                                <div class="col-md-9">
                                    <div class="blog_post">
                                        <a href="posts/{{$post->id}}"><img src="/storage/cover_imgs/{{$post->cover_img}}" alt=""></a>
                                        <div class="blog_details">
                                            <a href="posts/{{$post->id}}"><h2>{{$post->title}}</h2></a>
                                            <p>MCSE boot camps have its supporters and its detractors. Some people do not understand why you should have to spend money on boot camp when you can get the MCSE study materials yourself at a fraction.</p>
                                            <a href="#"><button class="view_btn button_hover">View More</button></a>
                                        </div>
                                    </div>
                                </div>
                                
                            </article>
                        @endif
                    @endforeach
                    <div class="paginationx">{{$posts->links()}}</div>
                @else
                    <p>No Posts found</p>
                @endif


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
                                <img class="author_img rounded-circle" src="maindir/image/blog/author.png" alt="">
                                <h4>Charlie Barber</h4>
                                <p>Senior blog writer</p>
                                <div class="social_icon">
                                    <a href="maindir/#"><i class="fa fa-facebook"></i></a>
                                    <a href="maindir/#"><i class="fa fa-twitter"></i></a>
                                    <a href="maindir/#"><i class="fa fa-github"></i></a>
                                    <a href="maindir/#"><i class="fa fa-behance"></i></a>
                                </div>
                                <p>Boot camps have its supporters andit sdetractors. Some people do not understand why you should have to spend money on boot camp when you can get. Boot camps have itssuppor ters andits detractors.</p>
                                <div class="br"></div>
                            </aside>
                            <aside class="single_sidebar_widget popular_post_widget">
                                <h3 class="widget_title">Popular Posts</h3>
                                <div class="media post_item">
                                    <img src="maindir/image/blog/post1.jpg" alt="post">
                                    <div class="media-body">
                                        <a href="maindir/blog-details.html"><h3>Space The Final Frontier</h3></a>
                                        <p>02 Hours ago</p>
                                    </div>
                                </div>
                                <div class="media post_item">
                                    <img src="maindir/image/blog/post2.jpg" alt="post">
                                    <div class="media-body">
                                        <a href="maindir/blog-details.html"><h3>The Amazing Hubble</h3></a>
                                        <p>02 Hours ago</p>
                                    </div>
                                </div>
                                <div class="media post_item">
                                    <img src="maindir/image/blog/post3.jpg" alt="post">
                                    <div class="media-body">
                                        <a href="maindir/blog-details.html"><h3>Astronomy Or Astrology</h3></a>
                                        <p>03 Hours ago</p>
                                    </div>
                                </div>
                                <div class="media post_item">
                                    <img src="maindir/image/blog/post4.jpg" alt="post">
                                    <div class="media-body">
                                        <a href="maindir/blog-details.html"><h3>Asteroids telescope</h3></a>
                                        <p>01 Hours ago</p>
                                    </div>
                                </div>
                                <div class="br"></div>
                            </aside>
                            <aside class="single_sidebar_widget ads_widget">
                                <a href="maindir/#"><img class="img-fluid" src="maindir/image/blog/add.jpg" alt=""></a>
                                <div class="br"></div>
                            </aside>
                            <aside class="single_sidebar_widget post_category_widget">
                                <h4 class="widget_title">Post Catgories</h4>
                                <ul class="list_style cat-list">
                                    <li>
                                        <a href="maindir/#" class="d-flex justify-content-between">
                                            <p>Technology</p>
                                            <p>37</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="maindir/#" class="d-flex justify-content-between">
                                            <p>Lifestyle</p>
                                            <p>24</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="maindir/#" class="d-flex justify-content-between">
                                            <p>Fashion</p>
                                            <p>59</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="maindir/#" class="d-flex justify-content-between">
                                            <p>Art</p>
                                            <p>29</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="maindir/#" class="d-flex justify-content-between">
                                            <p>Food</p>
                                            <p>15</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="maindir/#" class="d-flex justify-content-between">
                                            <p>Architecture</p>
                                            <p>09</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="maindir/#" class="d-flex justify-content-between">
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
                                    <a href="maindir/#" class="bbtns">Subcribe</a>
                                </div>	
                                <p class="text-bottom">You can unsubscribe at any time</p>	
                                <div class="br"></div>							
                            </aside>
                            <aside class="single-sidebar-widget tag_cloud_widget">
                                <h4 class="widget_title">Tag Clouds</h4>
                                <ul class="list_style">
                                    <li><a href="maindir/#">Technology</a></li>
                                    <li><a href="maindir/#">Fashion</a></li>
                                    <li><a href="maindir/#">Architecture</a></li>
                                    <li><a href="maindir/#">Fashion</a></li>
                                    <li><a href="maindir/#">Food</a></li>
                                    <li><a href="maindir/#">Technology</a></li>
                                    <li><a href="maindir/#">Lifestyle</a></li>
                                    <li><a href="maindir/#">Art</a></li>
                                    <li><a href="maindir/#">Adventure</a></li>
                                    <li><a href="maindir/#">Food</a></li>
                                    <li><a href="maindir/#">Lifestyle</a></li>
                                    <li><a href="maindir/#">Adventure</a></li>
                                </ul>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Blog Area =================-->

@endsection