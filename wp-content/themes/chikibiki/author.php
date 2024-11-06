<?php    get_header() ?>

        <section class="page-header">
           <div class="bg-shape grey"></div>
            <div class="container">
                <div class="page-header-content">
                   <h4> Blogs of author  </h4>
                    <h2>Book of Recipes and <br>Cooking Tips!</h2>
                    <p>Food is any substance consumed to provide nutritional <br>support for an organism.</p>
                </div>
            </div>
        </section>


        <section class="blog-section bg-grey padding">
            <div class="bg-shape white"></div>
            <div class="container">

                <div class="row blog-posts">
 
                    <div class="col-lg-8 col-md-12 sm-padding">

                        <div class="row classic-layout">
                        <?php if (have_posts()):
  while(have_posts()):

      the_post(); ?>

                            <div class="col-lg-12 sm-padding">

                                <div class="post-card">
                                    <div class="post-thumb">
                                        <img src="<?php the_post_thumbnail_url(); ?>" alt="img">
                                        <div class="category"><a href="#"><?php the_category(); ?></a></div>
                                    </div>
                                    <div class="post-content">
                                        <ul class="post-meta">
                                           <li><i class="far fa-calendar-alt"></i><a href="#"><?php the_date(); ?></a></li>
                                           <li><i class="far fa-user"></i><a href="#"><?php the_author_posts_link(); ?></a></li>
                                           <li><i class="far fa-comments"></i><a href="#">5 Comments</a></li>
                                        </ul>
                                        <h3><a href=""><?php the_title(); ?></a></h3>
                                        <p><?php the_excerpt(); ?></p>
                                        <a href="<?php the_permalink(); ?>" class="read-more">Read More <i class="las la-long-arrow-alt-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            <?php  endwhile;
            else:
              echo "There are NO articles";
            endif; ?>


                            
   

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
