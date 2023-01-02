
@extends('layouts.app')

@section('content')

  <div class="site-wrap">

    <div class="main_container">
      <div id="content_title">
        <h3 class="ctitle">Gallery</h3>
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

  </div> <!-- .site-wrap -->

@endsection
