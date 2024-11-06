<?php
/**
 * Template Name: Custom Shop Page
 */

get_header();

// Start WooCommerce's main content hook
do_action('woocommerce_before_main_content'); ?>

<section class="food-menu bg-grey pt-80">
    <div class="container">
        <div class="heading-wrap">
            <div class="section-heading mb-30">
                <h4>Popular Dishes</h4>
                <h2>Our Popular <span>Dishes</span></h2>
                <p>Food is any substance consumed to provide nutritional <br> support for an organism.</p>
            </div>
            <div>
                <p>Food is any substance consumed to provide nutritional <br> support for an organism.</p>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="default-btn"><i class="fas fa-utensils"></i>Full Menu <span></span></a>
            </div>
        </div>
        
        <div class="row">
            <?php
            // Query for popular WooCommerce products
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 6,
                'meta_key' => 'total_sales',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
            );
            $loop = new WP_Query($args);
            
            while ($loop->have_posts()) : $loop->the_post();
                global $product;
            ?>
            <div class="col-lg-4 col-sm-6 padding-15">
                <div class="product-item">
                    <?php if ($product->is_on_sale()) : ?>
                        <div class="sale">-<?php echo esc_html($product->get_sale_percentage()); ?>%</div>
                    <?php endif; ?>
                    
                    <div class="product-thumb">
                        <a href="<?php the_permalink(); ?>">
                            <?php woocommerce_show_product_sale_flash(); ?>
                            <?php echo woocommerce_get_product_thumbnail(); ?>
                        </a>
                        <div><a class='order-btn' href="<?php the_permalink(); ?>">Order Now</a></div>
                    </div>
                    
                    <div class="food-info">
                        <ul class="ratting">
                            <li><?php echo wc_get_product_category_list($product->get_id()); ?></li>
                            <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                        </ul>
                        <h3><?php the_title(); ?></h3>
                        <div class="price">
                            <h4>Price: <span><?php echo $product->get_price_html(); ?></span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; wp_reset_query(); ?>
        </div>
        
        <ul class="pagination-wrap text-center mt-30">
            <?php woocommerce_pagination(); ?>
        </ul>
    </div>
</section>

<?php
// End WooCommerce's main content hook
do_action('woocommerce_after_main_content');

get_footer();
