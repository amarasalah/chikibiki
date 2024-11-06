<?php  get_header(); ?>
<?php 
         // Start the Loop
        if ( have_posts() ) : 
            while ( have_posts() ) : the_post(); ?>
<section class="page-header">
           <div class="bg-shape white"></div>
            <div class="container">
                <div class="page-header-content">
                    <h4>        <?php 
                        // Display the page content
                        the_title(); 
                        ?></h4>
                    <h2><?php the_title(); ?></h2>
                  
                </div>
            </div>
        </section>



    </main><!-- .main-content -->
</div><!-- .page-container -->
<section class="team-section bg-grey padding">
           <div class="bg-shape white"></div>
            <div class="container">
                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php 
                        // Display the page content
                        the_content(); 
                        ?></div>
            </div>
        </section>
        <?php 
            endwhile; 
        else : 
            // If no content is available
            echo '<p>No content available</p>';
        endif;
        ?>



<?php get_footer();  ?>