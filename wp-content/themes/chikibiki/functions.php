<?php 
add_theme_support( 'post-thumbnails' ); 

function chikibiki_enqueue_stylesheet(){
    wp_enqueue_style('chikibiki-style', get_stylesheet_uri());
}

add_action('wp_enqueue_scripts', 'chikibiki_enqueue_stylesheet');
function chikibiki_register_menus(){
    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu' ),
            'footer' => __( 'Footer Menu' ),
        )
    );

}
add_action("init", 'chikibiki_register_menus');

  function chikibiki_register_widgets(){
$footer1=array(
     'name' => __( 'Footer 1'),
     'id'   => 'footer-1',
     'description' => __( 'Widgets in this area will be shown in the footer.'),
     'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
     'after_widget'  => '</div>',
     'before_title'  => '<h3 >',
     'after_title'   => '</h3>',
 
);


     register_sidebar($footer1);
  }
  add_action("widgets_init", 'chikibiki_register_widgets');

  
  if(! function_exists('chikibiiki_theme_setup')){

 
  function chikibiiki_theme_setup(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support("custom-header");
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 500,
        'flex-height' => true,
        'flex-width'  => true,
        // 'default-image' => get_template_directory_uri(). '/img/logo.png',
         //'default-text-color' => '#fff',
         'header-text' => array(
             'site-title',
             'site-description',
         ),
    ));


  } }
  add_action('after_setup_theme', 'chikibiiki_theme_setup'); 
  

//   add_filter('woocommerce_sale_flash', 'custom_sale_percentage_badge', 20, 3);
// function custom_sale_percentage_badge($text, $post, $product) {
//     $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
//     return '<span class="sale">-' . esc_html($percentage) . '%</span>';
// }
add_filter('woocommerce_sale_flash', 'custom_sale_badge', 20, 3);
function custom_sale_badge($html, $post, $product) {
    return '<span class="sale">' . esc_html__('SALE', 'your-theme-textdomain') . '</span>';
}

add_filter('woocommerce_product_loop_start', 'custom_woocommerce_product_loop_start', 10);

function custom_woocommerce_product_loop_start() {
    // Return custom opening div or ul tag with a new class
    return '<ul class="row">';
}

// Modify WooCommerce product loop end to close the custom wrapper
add_filter('woocommerce_product_loop_end', 'custom_woocommerce_product_loop_end', 10);

function custom_woocommerce_product_loop_end() {
    return '</ul>'; // Close the custom div or ul tag
}
// // Remove result count
// remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);

// // Remove sorting dropdown
// remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

// Add custom shop header content
function custom_shop_header_content() {
    ?>
    <div class="heading-wrap">
        <div class="section-heading mb-30">
            <h4>Popular Dishes</h4>
            <h2>Our Bestselling <span>Dishes</span></h2>
            <p>Food is any substance consumed to provide nutritional <br> support for an organism.</p>
        </div>
        <div>
            <p>Food is any substance consumed to provide nutritional <br> support for an organism.</p>
            <a class="default-btn" href="food-menu.html">
                <i class="fas fa-utensils"></i>Full Menu <span></span>
            </a>
        </div>
    </div>
    <?php
}
add_action( 'woocommerce_before_shop_loop', 'custom_shop_header_content', 5 );


//set the single product page for wordpress woocommerce
function mytheme_custom_cart_shortcode() {
    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
        return '<p>WooCommerce is not available or the cart is not initialized.</p>';
    }

    ob_start();

    if ( WC()->cart->is_empty() ) {
        echo '<p>Your cart is currently empty.</p>';
        return ob_get_clean();
    }

    ?>
    <section class="cart-section bg-grey padding">
        <div class="container">
            <div class="row cart-header">
                <div class="col-lg-6">Product</div>
                <div class="col-lg-3">Quantity</div>
                <div class="col-lg-1">Price</div>
                <div class="col-lg-1">Total</div>
                <div class="col-lg-1"></div>
            </div>

            <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : 
                $product = $cart_item['data'];
                $product_permalink = $product->is_visible() ? $product->get_permalink( $cart_item ) : '';

                // Get the product image URL and manually build <img> tag without width/height
                $image_url = wp_get_attachment_url( $product->get_image_id() );
                $image_html = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '" class="custom-product-image">';
            ?>
            <div class="row cart-body pb-30">
                <div class="col-lg-6">
                    <div class="cart-item">
                        <?php echo $image_html; // Display custom image HTML without width/height ?>
                        <div class="cart-content">
                            <h3>
                                <?php if ( ! $product_permalink ) : ?>
                                    <?php echo $product->get_name(); ?>
                                <?php else : ?>
                                    <a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo $product->get_name(); ?></a>
                                <?php endif; ?>
                            </h3>
                            <p><?php echo wp_strip_all_tags( $product->get_short_description() ); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-4 col-lg-3">
                    <div class="cart-item">
                        <?php
                            woocommerce_quantity_input(
                                array(
                                    'input_name' => "cart[{$cart_item_key}][qty]",
                                    'input_value' => $cart_item['quantity'],
                                    'min_value'   => '1',
                                    'max_value'   => $product->get_max_purchase_quantity(),
                                ),
                                $product
                            );
                        ?>
                    </div>
                </div>
                <div class="col-3 col-lg-1">
                    <div class="cart-item">
                        <p><?php echo wc_price( $product->get_price() ); ?></p>
                    </div>
                </div>
                <div class="col-3 col-lg-1">
                    <div class="cart-item">
                        <p><?php echo wc_price( $cart_item['line_total'] ); ?></p>
                    </div>
                </div>
                <div class="col-2 col-lg-1">
                    <div class="cart-item">
                        <a class="remove" href="<?php echo wc_get_cart_remove_url( $cart_item_key ); ?>"><i class="las la-times"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="row">
                <div class="col-lg-6 offset-lg-6">
                    <ul class="cart-total mt-30">
                        <li><span>Subtotal:</span><?php echo WC()->cart->get_cart_subtotal(); ?></li>
                        <li><span>Estimated shipping:</span><?php echo wc_price( WC()->cart->get_shipping_total() ); ?></li>
                        <li><span>Total:</span><?php echo WC()->cart->get_total(); ?></li>
                        <li>
                            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Continue Shopping</a>
                            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="default-btn">Checkout <span></span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode( 'custom_cart', 'mytheme_custom_cart_shortcode' );

// add_shortcode( 'custom_cart', 'mytheme_custom_cart_shortcode' );

// Add custom WooCommerce checkout shortcode
function custom_woocommerce_checkout_shortcode() {
    ob_start();

    // Check if WooCommerce is active
    if ( ! function_exists( 'WC' ) ) {
        return 'WooCommerce is not active.';
    }

    // Start the WooCommerce checkout process
    if ( is_checkout() ) {
        ?>
        <section class="checkout-section bg-grey padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 sm-padding">
                        <form action="<?php echo esc_url( wc_get_checkout_url() ); ?>" method="post" class="checkout-form-wrap" id="checkout-form">
                            <h2>Billing Details</h2>
                            <div class="checkout-form mb-30">
                                <?php
                                // Display each field with placeholders and without labels
                                $fields = [
                                    'billing_first_name' => __('First Name'),
                                    'billing_last_name' => __('Last Name'),
                                    'billing_company' => __('Company Name'),
                                    'billing_country' => __('Country'),
                                    'billing_city' => __('City'),
                                    'billing_state' => __('State / Province'),
                                    'billing_address_1' => __('Street'),
                                    'billing_postcode' => __('Post Code'),
                                    'billing_phone' => __('Phone'),
                                    'billing_email' => __('Email'),
                                ];

                                foreach ( $fields as $field_key => $placeholder ) {
                                    $type = ($field_key == 'billing_email') ? 'email' : 'text';
                                    $is_select = $field_key == 'billing_country';
                                    
                                    echo '<div class="form-field">';
                                    
                                    if ($is_select) {
                                        woocommerce_form_field($field_key, [
                                            'type' => 'select',
                                            'class' => ['form-control'],
                                            'options' => WC()->countries->get_countries(),
                                            'placeholder' => $placeholder,
                                            'required' => true,
                                            'label' => false,
                                        ]);
                                    } else {
                                        woocommerce_form_field($field_key, [
                                            'type' => $type,
                                            'class' => ['form-control'],
                                            'placeholder' => $placeholder,
                                            'required' => true,
                                            'label' => false,
                                        ]);
                                    }

                                    echo '</div>';
                                }
                                ?>
                            </div>

                            <div class="additional-info mb-30">
                                <h2>Additional Information</h2>
                                <div class="form-field">
                                    <textarea id="order_comments" name="order_comments" cols="30" rows="3" class="form-control" placeholder="Order Note"><?php if ( ! empty( $_POST['order_comments'] ) ) echo esc_textarea( $_POST['order_comments'] ); ?></textarea>
                                </div>
                            </div>

                            <div class="payment-method">
                                <h2>Payment Method</h2>
                                <?php
                                // Display payment methods
                                if ( ! WC()->cart->is_empty() ) {
                                    woocommerce_checkout_payment();
                                }
                                ?>
                                <button type="submit" class="default-btn">Place Order <span></span></button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-lg-4 sm-padding">
                        <ul class="cart-total">
                            <li><span>Subtotal:</span><?php echo wc_price(WC()->cart->subtotal); ?></li>
                            <li><span>Estimated shipping:</span><?php echo wc_price(WC()->cart->get_shipping_total()); ?></li>
                            <li><span>Total:</span><?php echo wc_price(WC()->cart->total); ?></li>
                            <li><a href='<?php echo esc_url( home_url( '/shop' ) ); ?>'>Continue Shopping</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <?php
    } else {
        return 'You are not on the checkout page.';
    }

    return ob_get_clean();
}

// Register the shortcode
add_shortcode('custom_checkout', 'custom_woocommerce_checkout_shortcode');


function caferio_about_page() {
    ob_start(); // Start output buffering
    ?>
    <section class="about-section inner padding">
        <div class="bg-shape grey"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div id="gallery-videos-demo" class="content-img-holder video-popup">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about02.png" alt="img">
                        <a class="play-btn" data-autoplay="true" data-vbtype="video" href="https://www.youtube.com/watch?v=FVdr12UJbuM">
                            <span class="play-icon"><i class="fas fa-play"></i></span>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="about-info">
                        <h2 class="mb-20">Caferio, Burgers, And <br> Best Pizzas <span>in Town!</span></h2>
                        <p>The restaurants in Hangzhou also catered to many northern Chinese who had fled south from Kaifeng during the Jurchen invasion of the 1120s, while it is also known that many restaurants were run by families.</p>
                        <p>Food is any substance consumed to provide nutritional support for an organism. Everyone just loves Italian foods, because it's delicious.</p>
                        <a href="#" class="default-btn">Order Now <span></span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content-section-2 bg-grey padding">
        <div class="bg-shape white"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="section-heading">
                        <h4>Caferio History</h4>
                        <h2>Restaurant is Like a Theater <br>Our Task is To <span>Amaze You!</span></h2>
                        <p>The restaurants in Hangzhou also catered to many northern Chinese who had fled south from Kaifeng during the Jurchen invasion of the 1120s, while it is also known that many restaurants were run by families.</p>
                        <img class="sign" src="<?php echo get_template_directory_uri(); ?>/assets/img/signature.png" alt="sign">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="content-img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/about03.png" alt="img">
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="testimonial-section bg-grey padding">
        <div class="container">
            <div class="section-heading mb-30 text-center">
                <h4>Testimonials</h4>
                <h2>Our Customers <span>Reviews</span></h2>
                <p>Food is any substance consumed to provide nutritional <br> support for an organism.</p>
            </div>
            <div class="nav-outside">
                <div class="testimonial-carousel swiper-container nav-visible">
                    <div class="swiper-wrapper">
                        <?php
                        $testimonials = [
                            ['name' => 'Robert William', 'role' => 'CEO Kingfisher', 'img' => 'testi01.jpg', 'text' => 'I would be lost without restaurant. I would like to personally thank you for your outstanding product.'],
                            ['name' => 'Thomas Josef', 'role' => 'CEO Getforce', 'img' => 'testi02.jpg', 'text' => 'I would be lost without restaurant. I would like to personally thank you for your outstanding product.'],
                            ['name' => 'Charles Richard', 'role' => 'CEO Angela', 'img' => 'testi03.jpg', 'text' => 'I would be lost without restaurant. I would like to personally thank you for your outstanding product.'],
                        ];
                        foreach ($testimonials as $testimonial) {
                            ?>
                            <div class="swiper-slide">
                                <div class="testimonial-item">
                                    <div class="testi-thumb">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/<?php echo $testimonial['img']; ?>" alt="img">
                                        <div class="author">
                                            <h3><?php echo $testimonial['name']; ?></h3>
                                            <h4><?php echo $testimonial['role']; ?></h4>
                                        </div>
                                    </div>
                                    <p>"<?php echo $testimonial['text']; ?>"</p>
                                    <ul class="ratting">
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                    </ul>
                                </div>
                            </div>
                            <?php
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

    <section class="content-section delivery">
        <div class="bg-shape white"></div>
        <div class="bg-shape grey"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="content-info">
                        <h2>A Moments Of Delivered <br> On <span>Right Time</span> &amp; Place</h2>
                        <p>The restaurants in Hangzhou also catered to many northern Chinese who had fled south from Kaifeng during the Jurchen invasion of the 1120s, while it is also known that many restaurants were run by families.</p>
                        <div class="order-content">
                            <a href="#" class="default-btn">Order Now <span></span></a>
                            <h3><span>Order Number</span>012-345-6789</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="delivery-girl">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/delivery.png" alt="img">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean(); // Return the buffered content
}
add_shortcode('caferio_about', 'caferio_about_page');
