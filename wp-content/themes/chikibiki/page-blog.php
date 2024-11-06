
<?php
/* Template Name: Blog Classic */
get_header();
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div class="post-card">
        <div class="post-thumb">
            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
        </div>
        <div class="post-content">
            <ul class="post-meta">
                <li><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></li>
                <li><i class="far fa-user"></i> <?php the_author_posts_link(); ?></li>
                <li><i class="far fa-comments"></i> <?php comments_number( '0 Comments', '1 Comment', '% Comments' ); ?></li>
            </ul>
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p><?php the_excerpt(); ?></p>
            <a href="<?php the_permalink(); ?>" class="read-more">Read More <i class="las la-long-arrow-alt-right"></i></a>
        </div>
    </div>
<?php endwhile; endif; ?>
<?php get_footer(); ?>
