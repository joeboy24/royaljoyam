


                        @foreach ($comments as $comment)
                            <div class="comment-list">
                                <div class="single-comment justify-content-between d-flex">
                                    <div class="user justify-content-between d-flex">
                                        <div class="thumb">
                                            <img src="/maindir/image/blog/c1.jpg" alt="">
                                        </div>
                                        <div class="desc">
                                            <h5><a href="/maindir/#">Emilly Blunt</a></h5>
                                            <p class="date">December 4, 2017 at 3:12 pm </p>
                                            <p class="comment">
                                                {{$comment->cbody}} Never say goodbye till the end comes!
                                            </p>
                                        </div>
                                    </div>
                                    <div class="reply-btn">
                                           <a href="/maindir/" class="btn-reply text-uppercase">reply</a> 
                                    </div>
                                </div>
                            </div>	
                        @endforeach