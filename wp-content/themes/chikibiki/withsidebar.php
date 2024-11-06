<?php   /* Template Name: with sidebar Temp */ get_header() ?>

        <section class="page-header">
           <div class="bg-shape grey"></div>
            <div class="container">
                <div class="page-header-content">
                   <h4>Recent Posts</h4>
                    <h2>Book of Recipes and <br>Cooking Tips!</h2>
                    <p>Food is any substance consumed to provide nutritional <br>support for an organism.</p>
                </div>
            </div>
        </section><!--/.page-header-->

        <section class="blog-section bg-grey padding">
            <div class="bg-shape white"></div>
            <div class="container">
                <div class="row blog-posts">
                    <div class="col-lg-8 col-md-12 sm-padding">
                        <div class="row classic-layout">
                            <div class="col-lg-12 sm-padding">
                                <div class="post-card">
                                    <div class="post-thumb">
                                        <img src="assets/img/post-1.jpg" alt="img">
                                        <div class="category"><a href="#">Design</a></div>
                                    </div>
                                    <div class="post-content">
                                        <ul class="post-meta">
                                           <li><i class="far fa-calendar-alt"></i><a href="#">Jan 01 2021</a></li>
                                           <li><i class="far fa-user"></i><a href="#">Jonathan Smith</a></li>
                                           <li><i class="far fa-comments"></i><a href="#">5 Comments</a></li>
                                        </ul>
                                        <h3><a href="#">Incredible Vegan Mac and Cheese.</a></h3>
                                        <p>Financial experts support or help you to to find out which way you can raise your funds more. Arkit a trusted name for providing assistants. Initially their main objective was to ensure the service they provide these people are loyal to their industry.</p>
                                        <a href="#" class="read-more">Read More <i class="las la-long-arrow-alt-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 sm-padding">
                                <div class="post-card">
                                    <div class="post-thumb">
                                        <img src="assets/img/post-2.jpg" alt="img">
                                        <div class="category"><a href="#">Business</a></div>
                                    </div>
                                    <div class="post-content">
                                       <ul class="post-meta">
                                           <li><i class="far fa-calendar-alt"></i><a href="#">Jan 01 2021</a></li>
                                           <li><i class="far fa-user"></i><a href="#">Jonathan Smith</a></li>
                                           <li><i class="far fa-comments"></i><a href="#">5 Comments</a></li>
                                        </ul>
                                        <h3><a href="#">Beet and Burrata Salad with Fried.</a></h3>
                                        <p>Financial experts support or help you to to find out which way you can raise your funds more. Arkit a trusted name for providing assistants. Initially their main objective was to ensure the service they provide these people are loyal to their industry.</p>
                                        <a href="#" class="read-more">Read More <i class="las la-long-arrow-alt-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="pagination-wrap text-left mt-30">
                            <li><a href="#"><i class="las la-arrow-left"></i></a></li>
                            <li><a href="#">1</a></li>
                            <li><a href="#" class="active">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#"><i class="las la-arrow-right"></i></a></li>
                        </ul><!--/.pagination -->
                    </div><!--/. col-lg-8 -->
<?php get_sidebar();?>
                </div>
            </div>
        </section><!--/. blog-section -->

<?php get_footer(); ?>
