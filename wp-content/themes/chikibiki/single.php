<?php  get_header(); ?>
<?php if (have_posts()) :
    while (have_posts()) : the_post(); ?>
        <section class="page-header">
            <div class="bg-shape grey"></div>
            <div class="container">
                <div class="page-header-content">
                    <h4>Blog Details</h4>
                    <h2><?php the_title(); ?><br>Salad with Fried.</h2>
                    <p>Food is any substance consumed to provide nutritional <br>support for an organism.</p>
                </div>
            </div>
        </section><!--/.page-header-->
    
   

        <section class="blog-section bg-grey padding">
            <div class="bg-shape white"></div>
            <div class="container">
                <div class="row blog-posts">
                    <div class="col-lg-8 col-md-12 sm-padding">
                        <div class="row single-layout">
                            <div class="col-lg-12 sm-padding">
                                <div class="post-card">
                                    <div class="post-thumb">
                                        <img src="<?php echo get_template_directory_uri();?>/assets/img/post-1.jpg" alt="img">
                                        <div class="category"><a href="#"><?php the_category(); ?></a></div>
                                    </div>
                                    <div class="post-content">
                                        <ul class="post-meta">
                                           <li><i class="far fa-calendar-alt"></i><a href="#"><?php the_date(); ?></a></li>
                                           <li><i class="far fa-user"></i><a href="#"><?php the_author(); ?></a></li>
                                           <li><i class="far fa-comments"></i><a href="#"><?php comments_number() ?></a></li>
                                        </ul>
                                        <h3><a href="#">><?php the_title(); ?></a></h3>
                                        <p><?php the_content(); ?></p>
                                        <p>Unless you are the one who really cares about this, it is not terribly important. What all matters are how your hybrid mobile application development is going to work in the long run as no one will care about how it was built.</p>
                                        <ul class="single-post-list">
                                            <h4>Method of cooking:</h4>
                                            <li><i class="fas fa-check"></i>The new functions coming to construction for equipment mathematics.</li>
                                            <li><i class="fas fa-check"></i>Initially their main objective was to ensure the service.</li>
                                            <li><i class="fas fa-check"></i>transformation on the horizon is one where advanced streams</li>
                                            <li><i class="fas fa-check"></i>What all matters are how your hybrid mobile application.</li>
                                            <li><i class="fas fa-check"></i>There are some big shifts taking place in the field of construction.</li>
                                        </ul>
                                        <ul class="single-post-gallery">
                                            <li><img src="<?php echo get_template_directory_uri();?>/assets/img/post-1.jpg" alt="img"></li>
                                            <li><img src="<?php echo get_template_directory_uri();?>/assets/img/post-2.jpg" alt="img"></li>
                                        </ul>
                                        <p>There are some big shifts taking place in the field of construction equipment mathematics. Starting with the integration of mathematics devices in vehicles right from the manufacturers, to the standardization and integration of mathematics data across various business functions, the future of mathematics has never seemed so full of potential for fleet-based businesses.</p>
                                        <blockquote>
                                            There are no secrets to success. It is the result preparation, hard work and learning from failure.<span>- Winston Churchill.</span>
                                        </blockquote>
                                        <p>Another speaker, John Meuse, senior director of heavy equipment at Waste Management Inc., echoed this, citing a cost-saving of $17,000 for the company when it cut idling time of a single Caterpillar 966 wheel loader.</p>
                                        <ul class="tags">
                                            <li><a href="#">business</a></li>
                                            <li><a href="#">marketing</a></li>
                                            <li><a href="#">startup</a></li>
                                            <li><a href="#">design</a></li>
                                            <li><a href="#">consulting</a></li>
                                        </ul>
                                        <div class="author-box">
                                            <img src="<?php echo get_template_directory_uri();?>/assets/img/comment-1.png" alt="img">
                                            <div class="author-info">
                                                <h4>S M Mostain Billah</h4>
                                                <p>Wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot.</p>
                                            </div>
                                        </div><!--/.author-box -->
                                        <div class="post-navigation">
                                            <div class="nav prev" style="background-image: url(<?php echo get_template_directory_uri();?>/assets/img/post-1.jpg);">
                                                <h4><a href="#"><span><i class="las la-arrow-left"></i>Previous</span>How to go about initiating an startup in few days.</a></h4>
                                            </div>
                                            <div class="nav next" style="background-image: url(<?php echo get_template_directory_uri();?>/assets/img/post-2.jpg);">
                                                <h4><a href="#"><span>Next<i class="las la-arrow-right"></i></span>How to go about initiating an startup in few days.</a></h4>
                                            </div>
                                        </div><!--/.post-navigation -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="comments-area">
                            <div class="comments-section">
                                <h3 class="single-post-tittle">Posts Comments <span></span></h3>
                                <ol class="comments">
                                    <li class="comment even thread-even depth-1" id="comment-1">
                                        <div id="div-comment-1">
                                            <div class="comment-thumb">
                                                <div class="comment-img"><img src="<?php echo get_template_directory_uri();?>/assets/img/comment-1.png" alt=""></div>
                                            </div>
                                            <div class="comment-main-area">
                                                <div class="comment-wrapper">
                                                    <div class="comments-meta">
                                                        <h4>Jhon Castellon <span class="comments-date">jan 05, 2022 at 8:00</span></h4>
                                                    </div>
                                                    <div class="comment-area">
                                                        <p>Home renovations, especially those involving plentiful of demolition can be a very dusty affair. This nasty dust can easily free flow through your house.</p>
                                                        <div class="comments-reply">
                                                            <a class="comment-reply-link" href="#"><span>Reply</span></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="children">
                                            <li class="comment">
                                                <div>
                                                    <div class="comment-thumb">
                                                        <div class="comment-img"><img src="<?php echo get_template_directory_uri();?>/assets/img/comment-2.png" alt=""></div>
                                                    </div>
                                                    <div class="comment-main-area">
                                                        <div class="comment-wrapper">
                                                            <div class="comments-meta">
                                                                <h4>José Carpio <span class="comments-date">jan 15, 2022 at 8:00</span></h4>
                                                            </div>
                                                            <div class="comment-area">
                                                                <p>Home renovations, especially those involving plentiful of demolition can be a very dusty affair. This nasty dust can easily free flow through your house.</p>
                                                                <div class="comments-reply">
                                                                    <a class="comment-reply-link" href="#"><span>Reply</span></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </li>
                                        </ul>
                                    </li>

                                </ol>
                            </div>
                        </div> <!--/.comments-area -->
                        <div class="comment-respond">
                            <h3 class="single-post-tittle">Write a Comment <span></span></h3>
                            <form method="post" id="commentform" class="comment-form">
                                <div class="form-textarea">
                                    <textarea id="comment" placeholder="Write Your Comments..."></textarea>
                                </div>
                                <div class="form-inputs">
                                    <input placeholder="Website" type="url">
                                    <input placeholder="Name" type="text">
                                    <input placeholder="Email" type="email">
                                </div>
                                <div class="form-submit">
                                    <input id="submit" value="Post Comment" type="submit">
                                </div>
                            </form>
                        </div>
                    </div><!--/. col-lg-8 -->
<?php get_sidebar() ?>
<!--/. col-lg-4 -->
                </div>
            </div>
        </section><!--/. blog-section -->        <?php endwhile;
    else : ?>
        <p>No content found</p>
    <?php endif; ?>


        <?php get_footer();  ?>