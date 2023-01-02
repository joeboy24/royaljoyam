@extends('layouts.app')

@section('content')
   <!--================Banner Area =================-->
        <section id="ban_area" class="banner_area">
            <div class="booking_table d_flex align-items-center">
            	<div class="overlay bg-parallax" data-stellar-ratio="0.9" data-stellar-vertical-offset="1" data-background=""></div>
				<div class="container">
					<div class="banner_content text-center">
						<h6>PivoApps</h6>
						<h2>Royal Joyam</h2>
						<h4>VENTURES</h4>
						<p>Access all of our products from<br> Any platform of your choice to make work more easier.</p>
						<a href="" data-toggle="modal" data-target="" class="btn theme_btn button_hover">Register With Us</a>
					</div>
				</div>
            </div>
            <!--div class="hotel_booking_area position">
                <div class="container">
                    <div class="hotel_booking_table">
                        <div class="col-md-3">
                            <h2>Book<br> Your Room</h2>
                        </div>
                        <div class="col-md-9">
                            <div class="boking_table">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="book_tabel_item">
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker11'>
                                                    <input type='text' class="form-control" placeholder="Arrival Date"/>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker1'>
                                                    <input type='text' class="form-control" placeholder="Departure Date"/>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="book_tabel_item">
                                            <div class="input-group">
                                                <select class="wide">
                                                    <option data-display="Adult">Adult</option>
                                                    <option value="1">Old</option>
                                                    <option value="2">Younger</option>
                                                    <option value="3">Potato</option>
                                                </select>
                                            </div>
                                            <div class="input-group">
                                                <select class="wide">
                                                    <option data-display="Child">Child</option>
                                                    <option value="1">Child</option>
                                                    <option value="2">Baby</option>
                                                    <option value="3">Child</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="book_tabel_item">
                                            <div class="input-group">
                                                <select class="wide">
                                                    <option data-display="Child">Number of Rooms</option>
                                                    <option value="1">Room 01</option>
                                                    <option value="2">Room 02</option>
                                                    <option value="3">Room 03</option>
                                                </select>
                                            </div>
                                            <a class="book_now_btn button_hover" href="maindir/#">Book Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div-->
        </section>
        <!--================Banner Area =================-->
        
        <!--================ Accomodation Area  =================-->
        <section class="accomodation_area section_gap">
            <div class="container">
                {{-- <div class="section_title text-center">
                    <h2 class="title_color">Hotel Accomodation</h2>
                    <p>We all live in an age that belongs to the young at heart. Life that is becoming extremely fast, </p>
                </div> --}}

                <div class="container-fluid">
                        @include('inc.messages')

                    <div class="row">
          
                      <div id="myCol" class="col-lg-4 col-md-6 col-sm-6">
          
                        <a href="#" data-toggle="" data-target="#" class="myA">
                          <div class="card myCard">
                            
                            <i class="fa fa-file-text myIcon"></i>
          
                            <h3 class='config'>Inventory</h3>
                              
                            <p>Click here to check manage items...</p>

                          </div>
                        </a>
                      </div>
          
                      <div id="myCol" class="col-lg-4 col-md-6 col-sm-6">
          
                        <a href="#" class="myA">
                          <div class="card myCard">
                            
                            <i class="fa fa-shopping-basket myIcon"></i>
          
                            <h3 class='config'>Sales</h3>
                            
                            <p>Click here to make sales...</p>

                          </div>
                        </a>
                      </div>
          
                      <div id="myCol" class="col-lg-4 col-md-6 col-sm-6">
          
                        <a href="#" class="myA">
                          <div class="card myCard">
                            
                            <i class="fa fa-print myIcon"></i>
          
                            <h3 class='config'>Reports</h3>
                            
                            <p>Click here to download/print receipt...</p>
                            
                          </div>
                        </a>
                      </div>
          
                    </div>
          
                  </div>

            </div>
        </section>
        <!--================ Accomodation Area  =================-->
        
        <!--================ Facilities Area  =================-->
        <!--section class="facilities_area section_gap">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="">  
            </div>
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_w">Royal Facilities</h2>
                    <p>Who are in extremely love with eco friendly system.</p>
                </div>
                <div class="row mb_30">
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-dinner"></i>Restaurant</h4>
                            <p>Usage of the Internet is becoming more common due to rapid advancement of technology and power.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-bicycle"></i>Sports CLub</h4>
                            <p>Usage of the Internet is becoming more common due to rapid advancement of technology and power.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-shirt"></i>Swimming Pool</h4>
                            <p>Usage of the Internet is becoming more common due to rapid advancement of technology and power.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-car"></i>Rent a Car</h4>
                            <p>Usage of the Internet is becoming more common due to rapid advancement of technology and power.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-construction"></i>Gymnesium</h4>
                            <p>Usage of the Internet is becoming more common due to rapid advancement of technology and power.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-coffee-cup"></i>Bar</h4>
                            <p>Usage of the Internet is becoming more common due to rapid advancement of technology and power.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section-->
        <!--================ Facilities Area  =================-->
        
        <!--================ About History Area  =================-->
        <!--section class="about_history_area section_gap">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 d_flex align-items-center">
                        <div class="about_content ">
                            <h2 class="title title_color">About Us <br>Our History<br>Mission & Vision</h2>
                            <p>inappropriate behavior is often laughed off as “boys will be boys,” women face higher conduct standards especially in the workplace. That’s why it’s crucial that, as women, our behavior on the job is beyond reproach. inappropriate behavior is often laughed.</p>
                            <a href="maindir/#" class="button_hover theme_btn_two">Request Custom Price</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img class="img-fluid" src="maindir/image/about_bg.jpg" alt="img">
                    </div>
                </div>
            </div>
        </section-->
        <!--================ About History Area  =================-->
        
        <!--================ Testimonial Area  =================-->
        <!--div class="site-wrap">

            <div class="main_container">
              <div id="content_title">
                <h1 class="ctitle">Gallery</h1>
                <p class="csubtitle">Find all you want here</p>
              </div>
              <main class="main-content">
                <div class="container-fluid photos">
                  <div class="row align-items-stretch">
                    
                    <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up">
                      <a href="/single" class="d-block photo-item">
                        <img src="/maindir/image/img_4.jpg" alt="Image" class="img-fluid">
                        <div class="photo-text-more">
                          <div class="photo-text-more">
                          <h3 class="heading">Photos Title Here</h3>
                          <span class="meta">42 Photos</span>
                        </div>
                        </div>
                      </a>
                    </div>
                    <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                      <a href="/single" class="d-block photo-item">
                        <img src="maindir/image/img_5.jpg" alt="Image" class="img-fluid">
                        <div class="photo-text-more">
                          <div class="photo-text-more">
                          <h3 class="heading">Photos Title Here</h3>
                          <span class="meta">42 Photos</span>
                        </div>
                        </div>
                      </a>
                    </div>
                    <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                      <a href="/single" class="d-block photo-item">
                        <img src="maindir/image/img_1.jpg" alt="Image" class="img-fluid">
                        <div class="photo-text-more">
                          <h3 class="heading">Photos Title Here</h3>
                          <span class="meta">42 Photos</span>
                        </div>
                      </a>
                    </div>
        
                    <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up">
                      <a href="/single" class="d-block photo-item">
                        <img src="maindir/image/img_2.jpg" alt="Image" class="img-fluid">
                        <div class="photo-text-more">
                          <div class="photo-text-more">
                          <h3 class="heading">Photos Title Here</h3>
                          <span class="meta">42 Photos</span>
                        </div>
                        </div>
                      </a>
                    </div>
                    <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                      <a href="/single" class="d-block photo-item">
                        <img src="maindir/image/img_3.jpg" alt="Image" class="img-fluid">
                        <div class="photo-text-more">
                          <div class="photo-text-more">
                          <h3 class="heading">Photos Title Here</h3>
                          <span class="meta">42 Photos</span>
                        </div>
                        </div>
                      </a>
                    </div>
                    <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                      <a href="/single" class="d-block photo-item">
                        <img src="maindir/image/img_6.jpg" alt="Image" class="img-fluid">
                        <div class="photo-text-more">
                          <div class="photo-text-more">
                          <h3 class="heading">Photos Title Here</h3>
                          <span class="meta">42 Photos</span>
                        </div>
                        </div>
                      </a>
                    </div>
        
        
                  </div>
                    </div>
                  </div>
                </div>
              </main>
            </div>
        
        </div--> <!-- .site-wrap -->
        <!--================ Testimonial Area  =================-->
        
        <!--================ Latest Blog Area  =================-->
        <!--section class="latest_blog_area section_gap">
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_color">latest posts from blog</h2>
                    <p>The French Revolution constituted for the conscience of the dominant aristocratic class a fall from </p>
                </div>
                <div class="row mb_30">
                    <div class="col-lg-4 col-md-6">
                        <div class="single-recent-blog-post">
                            <div class="thumb">
                                <img class="img-fluid" src="maindir/image/blog/blog-1.jpg" alt="post">
                            </div>
                            <div class="details">
                                <div class="tags">
                                    <a href="maindir/#" class="button_hover tag_btn">Travel</a>
                                    <a href="maindir/#" class="button_hover tag_btn">Life Style</a>
                                </div>
                                <a href="maindir/#"><h4 class="sec_h4">Low Cost Advertising</h4></a>
                                <p>Acres of Diamonds… you’ve read the famous story, or at least had it related to you. A farmer.</p>
                                <h6 class="date title_color">31st January,2018</h6>
                            </div>	
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-recent-blog-post">
                            <div class="thumb">
                                <img class="img-fluid" src="maindir/image/blog/blog-2.jpg" alt="post">
                            </div>
                            <div class="details">
                                <div class="tags">
                                    <a href="maindir/#" class="button_hover tag_btn">Travel</a>
                                    <a href="maindir/#" class="button_hover tag_btn">Life Style</a>
                                </div>
                                <a href="maindir/#"><h4 class="sec_h4">Creative Outdoor Ads</h4></a>
                                <p>Self-doubt and fear interfere with our ability to achieve or set goals. Self-doubt and fear are</p>
                                <h6 class="date title_color">31st January,2018</h6>
                            </div>	
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-recent-blog-post">
                            <div class="thumb">
                                <img class="img-fluid" src="maindir/image/blog/blog-3.jpg" alt="post">
                            </div>
                            <div class="details">
                                <div class="tags">
                                    <a href="maindir/#" class="button_hover tag_btn">Travel</a>
                                    <a href="maindir/#" class="button_hover tag_btn">Life Style</a>
                                </div>
                                <a href="maindir/#"><h4 class="sec_h4">It S Classified How To Utilize Free</h4></a>
                                <p>Why do you want to motivate yourself? Actually, just answering that question fully can </p>
                                <h6 class="date title_color">31st January,2018</h6>
                            </div>	
                        </div>
                    </div>
                </div>
            </div>
        </section-->
        <!--================ Recent Area  =================-->



        <div class="modal fade homelog" id="checkRes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;{{ __('Login') }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  


                        <div class="row justify-content-center">
                            <div class="col-md-10">
                    
                                    <div class="card-body">
                                        <form method="POST" action="">
                                            @csrf
                    
                                            <div class="form-group row">
                                                <label for="pass" class="col-md-4 col-form-label text-md-right">Student Id</label>
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control" name="name" value="202112" placeholder="Type Student Id Here!." required/>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="pass" class="col-md-4 col-form-label text-md-right">Access Code</label>
                                                <div class="col-md-7">
                                                    <input id="pass" type="password" class="form-control" value="Ex7721372A7T" required autofocus>
                    
                                                    {{-- @if ($errors->has('email'))
                                                        <span class="invalid-feedback" role="alert">
                                                        </span>
                                                    @endif --}}
                                                </div>
                                            </div>
                                            
                                            <div class="modal-footer">
                                                <a href="/resultcheck" class="btn btn-info"><i class="fa fa-folder-open"></i> &nbsp; Login</a>
                                              </div>
                    
                                        </form>
                                    </div>
                            </div>
                        </div>
                    


                    

                </div>
              </div>
            </div>
        </div>

  
@endsection
        
     