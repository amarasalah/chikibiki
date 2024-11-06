<?php  get_header(); ?>

        <section class="hero-section">
            <div class="container">
               <div class="hero-img wow fadeInRight" data-wow-delay="400ms">
                    <img src="<?php echo get_template_directory_uri();?>/assets/img/pizza.png" alt="pizza">
                    <div class="sale">
                       <div>
                           <h4>Get Up To</h4>
                           <h2><span>50 %</span>De réduction</h2>                        </div>
                    </div>
               </div>
                <div class="hero-content wow fadeInLeft" data-wow-delay="200ms">
                    <h3>Nos Spécialités</h3>
                    <h1>Brochettes  <br> <span> à l'unité !</span></h1>
                    <ul class="hero-list">
                        <li><i class="fas fa-check"></i>Hot &amp; Spicy</li>
                        <li><i class="fas fa-check"></i>100% Fresh</li>
                        <li><i class="fas fa-check"></i>Fast Delivery</li>
                    </ul>
                    <p>Découvrez nos délicieuses brochettes, préparées avec soin et vendues à l’unité.<br> Accompagnez-les de frites croustillantes, de riz savoureux, de couscous parfumé ou de salade fraîche.<br>Une expérience culinaire personnalisée à chaque commande ! </p>
                    <a class='default-btn' href='shop.html'><i class="fas fa-shopping-cart"></i>Commandez <span></span></a>
                </div>
            </div>
        </section><!--/.hero-section-->

        <section class="delivery-section padding">
            <div class="bg-shape grey"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 wow fadeInLeft" data-wow-delay="200ms">
                        <div class="delivery-info">
                            <h2><span>Kokopelli ,</span> le voyageur légendaire <br>a parcouru le monde entier avec sa flûte enchantée et son sac de graines magiques. </h2>
                            <p> Un jour, il est tombé sur le village pittoresque de Puceul. Fatigué mais émerveillé, il s’est reposé sous un arbre d’or magnifique. En jetant quelques graines magiques, il a créé un lieu spécial : Chiki Biki. Maintenant, Kokopelli vous invite à un festin de saveurs du monde entier. Préparez-vous à embarquer pour une aventure culinaire inoubliable !</p>
                            <div class="order-content">
                                <a class='default-btn' href='shop-details.html'>Commander maintenant <span></span></a>
                                <h3><span>Commander maintenant</span>0683153626</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="delivery-boy-wrap">
                            <img class="delivery" src="<?php echo get_template_directory_uri();?>/assets/img/cloud.png" alt="img">
                            <div class="delivery-boy"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!--/.delivery-section-->
        <section class="promo-section bg-grey padding">
    <div class="bg-shape white"></div>
    <div class="container">
        <div class="nav-outside">
            <div class="food-carousel swiper-container nav-visible">
                <div class="swiper-wrapper">
                    <?php
                    // WooCommerce function to get unique product categories
                    $args = array(
                        'taxonomy'   => 'product_cat',
                        'number'     => 5, // Limit the number of categories displayed
                        'hide_empty' => true, // Only show categories with products
                        'orderby'    => 'name', // Sort by name for consistency
                        'order'      => 'ASC',
                    );
                    $product_categories = get_terms($args);

                    // Debug: Check if categories are retrieved
                    if (empty($product_categories) || is_wp_error($product_categories)) {
                        echo '<p>No categories found or an error occurred.</p>';
                    } else {
                        $displayed_categories = array(); // Track displayed categories
                        foreach ($product_categories as $category) :
                            if (in_array($category->term_id, $displayed_categories)) {
                                continue; // Skip if this category has already been displayed
                            }
                            $displayed_categories[] = $category->term_id; // Mark category as displayed

                            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                            $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src();
                    ?>
                            <div class="swiper-slide">
                                <div class="food-item">
                                    <div class="food-icon">
                                        <?php
                                        // Display an icon based on category name or slug
                                        if ($category->slug === 'pizza') {
                                            echo '<i class="fi fi-pizza-slice"></i>';
                                        } elseif ($category->slug === 'drinks') {
                                            echo '<i class="fi fi-beer"></i>';
                                        } elseif ($category->slug === 'fries') {
                                            echo '<i class="fi fi-fried-potatoes"></i>';
                                        } elseif ($category->slug === 'burger') {
                                            echo '<i class="fi fi-burger"></i>';
                                        } elseif ($category->slug === 'chicken') {
                                            echo '<i class="fi fi-chicken-leg"></i>';
                                        } else {
                                            echo '<i class="fi fi-utensils"></i>'; // Default icon
                                        }
                                        ?>
                                    </div>
                                    <div class="food-content">
                                        <h3><?php echo esc_html($category->name); ?></h3>
                                        <p><?php echo esc_html($category->description); ?></p>
                                    </div>
                                    <div class="food-thumb">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($category->name); ?>">
                                    </div>
                                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="button">View Category</a>
                                </div>
                            </div>
                    <?php
                        endforeach;
                    }
                    ?>
                </div>
                <div class="dl-slider-controls style-2">
                    <div class="dl-slider-button-prev"><i class="las la-arrow-left"></i></div>
                    <div class="dl-swiper-pagination"></div>
                    <div class="dl-slider-button-next"><i class="las la-arrow-right"></i></div>
                </div>
                <div class="carousel-preloader"><div class="dot-flashing"></div></div>
            </div>
        </div>
    </div>
</section>

<!--/.promo-section-->

        <section class="about-section padding">
            <div class="bg-shape grey"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 wow fadeInLeft" data-wow-delay="200ms">
                        <div class="content-img-holder">
                            <img src="<?php echo get_template_directory_uri();?>/assets/img/about01.png" alt="img">
                            <div class="sale">
                                <div>
                                    <h4>Get Up To</h4>
                                    <h2><span>50%</span>Off Now</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 wow fadeInRight" data-wow-delay="400ms">
                        <div class="about-info">
                            <h2>Un Goût <span>Authentique</span> <br>Viande Fraîche & Frites Maison </h2>
                            <p>Chez Chiki Biki, nous croyons que la qualité des ingrédients fait toute la différence. C'est pourquoi nous vous proposons notre plat</p>
                            <ul class="check-list">
                                <li><i class="fas fa-check"></i>Viande fraîche</li>
                                <p>Chez Chiki Biki, nous nous engageons à n'utiliser que de la viande
                                    fraîche, rigoureusement sélectionnée pour vous garantir un goût authentique et une qualité exceptionnelle à chaque bouchée.</p>
                                <li><i class="fas fa-check"></i>Frites maison</li>
                                <p>Nos frites sont entièrement faites maison, préparées à partir de pommes de terre fraîches. Elles sont soigneusement coupées et frites pour vous offrir une texture croustillante et une saveur irrésistible.</p>

                            </ul>
                            <a class='default-btn' href='shop-details.html'>COMMANDEZ EN LIGNE <span></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </section><!--/.about-section-->
        <section class="food-menu bg-grey padding">
    <div class="container">
        <div class="section-heading mb-30 text-center wow fadeInUp" data-wow-delay="200ms">
            <h4>Popular Dishes</h4>
            <h2>Our Delicious <span>Foods</span></h2>
            <p>Food is any substance consumed to provide nutritional <br> support for an organism.</p>
        </div>

        <?php
        // Display WooCommerce product categories as filter buttons
        $product_cats = get_terms('product_cat', array('hide_empty' => true));
        if (!empty($product_cats)) {
            echo '<ul class="food-menu-filter">';
            echo '<li class="active" data-filter="*">All</li>';
            foreach ($product_cats as $cat) {
                echo '<li data-filter=".' . esc_attr($cat->slug) . '">' . esc_html($cat->name) . '</li>';
            }
            echo '</ul>';
        }
        ?>

        <div class="row product-items">
            <?php
            // Query WooCommerce products
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 6,
                'meta_query' => WC()->query->get_meta_query(),
                'tax_query' => WC()->query->get_tax_query(),
            );
            $loop = new WP_Query($args);

            if ($loop->have_posts()) :
                while ($loop->have_posts()) : $loop->the_post();
                    global $product;
                    $categories = get_the_terms(get_the_ID(), 'product_cat');
                    $category_class = '';

                    if (!empty($categories)) {
                        foreach ($categories as $category) {
                            $category_class .= ' ' . $category->slug;
                        }
                    }
                    ?>
                    <div class="col-lg-4 col-md-6 padding-15 isotop-grid <?php echo esc_attr($category_class); ?>">
                        <div class="product-item wow fadeInUp" data-wow-delay="200ms">
                            <?php if ($product->is_on_sale()) : ?>
                                <div class="sale"><?php echo wc_format_sale_price($product->get_regular_price(), $product->get_sale_price()); ?>%</div>
                            <?php endif; ?>
                            <div class="product-thumb">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo woocommerce_get_product_thumbnail(); ?>
                                </a>
                                <div><a class='order-btn' href='<?php the_permalink(); ?>'>Order Now</a></div>
                            </div>
                            <div class="food-info">
                                <ul class="ratting">
                                    <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                                </ul>
                                <h3><?php the_title(); ?></h3>
                                <div class="price">
                                    <h4>Price: 
                                        <span><?php echo $product->get_price_html(); ?></span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
            else :
                echo '<p>No products found.</p>';
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>
<!--/.food-menu-->



        <section class="content-section delivery">
            <div class="bg-shape white"></div>
            <div class="bg-shape grey"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 wow fadeInLeft" data-wow-delay="200ms">
                        <div class="content-info">
                            <h2>Commandez dès maintenant  <br> chez  <span>Chikibiki !</span> </h2>
                            <p>Profitez de nos plats délicieux et authentiques en commandant directement au restaurant Chikibiki ! Que vous ayez envie d'une spécialité savoureuse ou d'un plat réconfortant, nous sommes là pour satisfaire vos envies. Appelez-nous dès maintenant au <span>0683153626</span>  pour passer votre commande et découvrir nos offres spéciales du jour. Ne manquez pas l'occasion de déguster nos délices culinaires !</p>
                            <div class="order-content">
                               <a class='default-btn' href='shop.html'>Commandez dès maintenant  <span></span></a>
                                <h3><span>Commandez</span>0683153626</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="delivery-girl">
                            <img src="<?php echo get_template_directory_uri();?>/assets/img/delivery-girl01.png" alt="img">
                        </div>
                    </div>
                </div>
            </div>
        </section><!--/.content-section-->

        <section class="banner-section padding">
    <div class="bg-shape grey"></div>
    <div class="container">
        <div class="row banner-wrapper">
            <?php
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 4,
                'meta_query' => array(
                    array(
                        'key' => '_sale_price',
                        'value' => 0,
                        'compare' => '>',
                        'type' => 'NUMERIC'
                    )
                )
            );
            $loop = new WP_Query($args);
            $count = 0;
            while ($loop->have_posts()) : $loop->the_post();
                $count++;
                $delay = $count * 200;
                $product = wc_get_product(get_the_ID());
                $discount_percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
            ?>
                <div class="col-md-6 wow bg-danger fadeInUp" data-wow-delay="<?php echo $delay; ?>ms">
                    <div class="banner-item">
                        <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" alt="banner">
                        <div class="banner-content">
                            <h3>-<?php echo $discount_percentage; ?>% Off Now!</h3>
                            <h2><?php echo get_the_title(); ?></h2>
                            <p>Sale off <?php echo $discount_percentage; ?>% only this week</p>
                            <a class='order-btn' href='<?php echo get_permalink(); ?>'>Order Now</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_query(); ?>
        </div>
    </div>
</section>
><!--/.banner-section-->


<section class="testimonial-section bg-grey padding">
    <div class="bg-shape white"></div>
    <div class="container">
        <div class="section-heading mb-30 text-center wow fadeInUp" data-wow-delay="200ms">
            <h4>Testimonials</h4>
            <h2>Our Customers <span>Reviews</span></h2>
            <p>Food is any substance consumed to provide nutritional <br> support for an organism.</p>
        </div>
        <div class="nav-outside">
            <div class="testimonial-carousel swiper-container nav-visible">
                <div class="swiper-wrapper">
                    <?php
                    // Custom query to fetch testimonials
                    $args = array(
                        'post_type' => 'testimonial',
                        'posts_per_page' => 5, // Adjust if needed
                        'no_found_rows' => true, // Avoids additional DB queries for pagination
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    $testimonial_query = new WP_Query($args);

                    if ($testimonial_query->have_posts()) :
                        while ($testimonial_query->have_posts()) : $testimonial_query->the_post();
                            // Fetch ACF fields
                            $rating = get_field('rating');
                            $jobtitle = get_field('jobtitle'); // Ensure this matches your ACF field
                            $client_image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                            // $description =  the_content(); 

                            // if (!$description) continue; // Skip empty descriptions
                    ?>
                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div class="testi-thumb">
                                    <?php if ($client_image) : ?>
                                        <img src="<?php echo esc_url($client_image); ?>" alt="<?php the_title_attribute(); ?>">
                                    <?php endif; ?>
                                    <div class="author">
                                        <h3><?php the_title(); ?></h3>
                                        <h4><?php echo esc_html($jobtitle); ?></h4>
                                    </div>
                                </div>
                                <p>

                         
                                </p>
                                <ul class="rating">
                                    <?php 
                                    // Display rating stars
                                    for ($i = 0; $i < 5; $i++) {
                                        echo ($i < $rating) ? '<li><i class="las la-star"></i></li>' : '<li><i class="las la-star-o"></i></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
                <div class="dl-slider-controls style-2">
                    <div class="dl-slider-button-prev"><i class="las la-arrow-left"></i></div>
                    <div class="dl-swiper-pagination"></div>
                    <div class="dl-slider-button-next"><i class="las la-arrow-right"></i></div>
                </div>
                <div class="carousel-preloader"><div class="dot-flashing"></div></div>
            </div>
        </div>
    </div>
</section>
<!--/.testimonial-section-->



        <section class="blog-section bg-grey padding">
           <div class="bg-shape white"></div>
            <div class="container">
                <div class="section-heading mb-30 text-center wow fadeInUp" data-wow-delay="200ms">
                    <h4>Latest Blog Posts</h4>
                    <h2>This Is All About <span>Foods</span></h2>
                    <p>Food is any substance consumed to provide nutritional <br> support for an organism.</p>
                </div>
                <div class="row blog-posts">
                <?php

if (have_posts()):
  while(have_posts()):

      the_post(); ?>


                    <div class="col-lg-4 col-md-6 sm-padding wow fadeInUp" data-wow-delay="200ms">
                        <div class="post-card">
                            <div class="post-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/post-1.jpg" alt="img">
                                <div class="category"><a href="#"><?php the_category(); ?></a></div>
                            </div>
                            <div class="post-content">
                                <ul class="post-meta">
                                   <li><i class="far fa-calendar-alt"></i><a href="#"><?php the_date(); ?></a></li>
                                   <li><i class="far fa-user"></i><a href="#"><?php the_author(); ?></a></li>
                                </ul>
                                <h3><a href='<?php the_permalink(); ?>'><?php the_title(); ?></a></h3>
                                <p><?php the_excerpt(); ?></p>
                                <a class='read-more' href='<?php the_permalink(); ?>'>Read More <i class="las la-long-arrow-alt-right"></i></a>
                            </div>
                        </div>
                    </div>
                    

                    <?php  endwhile;
            else:
              echo "There are NO articles";
            endif; ?>

                </div>
            </div>
        </section><!-- /.blog-section -->
<?php get_footer();  ?>