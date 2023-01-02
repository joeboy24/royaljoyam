@extends('layouts.app')

@section('content')
   <!--================Banner Area =================-->
        <section class="banner_area">
            <div class="booking_table d_flex align-items-center">
            	<div class="overlay bg-parallax" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background=""></div>
				<div class="container">
					<div class="banner_content text-center">
						<h6>PivoApps</h6>
						<h2>School Track</h2>
						<p>Access all of our products from<br> Any platform of your choice to make work more easier.</p>
						<a href="" data-toggle="modal" data-target="#reg" class="btn theme_btn button_hover">Register</a>
					</div>
				</div>
            </div>
        </section>
        <!--================Banner Area =================-->
        
        <!--================ Accomodation Area  =================-->
        <section class="accomodation_area section_gap">
            {{-- <div class="container">
                
                <form action="{{url('/import')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @include('inc.messages')

                    <input type="file" id="file" name="file"/>
                    <button type="submit">Upload File</button>
                </form>

            </div> --}}


            <div class="container">

                @include('inc.messages')

                <div class="card bg-light mt-3">
                    <img class="img" src="/storage/app/public/tch_imgs/Lucy_1611703478" />
                    <div class="card-header">
                        Laravel 5.7 Import Export Excel to database Example - ItSolutionStuff.com
                    </div>
                    <div class="card-body">
                        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" class="form-control">
                            <br>
                            <button class="btn btn-success">Import User Data</button>
                            <a class="btn btn-warning" href="{{ route('export') }}">Export User Data</a>
                        </form>
                    </div>
                </div>
            </div>


        </section>
        <!--================ Accomodation Area  =================-->
        
       
@endsection
        
     