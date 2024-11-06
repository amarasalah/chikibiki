<?php
defined('ABSPATH') || exit;
get_header('shop');

// Get the global product object
global $product;
?>
        <section class="page-header">
           <div class="bg-shape grey"></div>
            <div class="container">
                <div class="page-header-content">
                   <h4>Recent Posts</h4>
                    <h2>Book of Recipes and <br>Cooking Tips!</h2>
                    <p>Food is any substance consumed to provide nutritional <br>support for an organism.</p>
                </div>
            </div>
        </section>

<section class="food-details bg-grey pt-80">
    <div class="container">
        <div class="row">
            <!-- Product Image Section -->
            <div class="col-md-6 sm-padding product-details-wrap">
                <div class="food-details-thumb">
                    <?php woocommerce_show_product_images(); ?>
                </div>
            </div>
            
            <!-- Product Details Section -->
            <div class="col-md-6 sm-padding">
                <div class="product-details">
                    <div class="product-info">
                        <div class="product-inner">
                            <ul class="category">
                                <li><?php echo wc_get_product_category_list($product->get_id()); ?></li>
                            </ul>
                            <ul class="ratting">
                                <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                            </ul>
                        </div>
                        <h3><?php the_title(); ?></h3>
                        <h4 class="price">
                            <?php echo $product->get_price_html(); ?> 
                            <span>(<?php echo $product->is_in_stock() ? 'In Stock' : 'Out of Stock'; ?>)</span>
                        </h4>
                        <p><?php the_excerpt(); ?></p>
                        
                        <div class="product-btn">
                            <?php woocommerce_template_single_add_to_cart(); ?>
                        </div>
                        
                        <ul class="product-meta">
                            <li>SKU: <?php echo $product->get_sku(); ?></li>
                            <li>Categories: <?php echo wc_get_product_category_list($product->get_id()); ?></li>
                            <li>Tags: <?php echo wc_get_product_tag_list($product->get_id()); ?></li>
                        </ul>
                        <ul class="social-icon">
                            <li>Share:</li>
                            <li><a href="#"><i class="lab la-facebook-f"></i></a></li>
                            <li><a href="#"><i class="lab la-twitter"></i></a></li>
                            <li><a href="#"><i class="lab la-behance"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="product-description bg-grey padding">
    <div class="container">
        <ul class="nav tab-navigation" id="product-tab-navigation" role="tablist">
            <li role="presentation">
                <button class="active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Ingredients</button>
            </li>
            <li role="presentation">
                <button id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Additional information</button>
            </li>
            <li role="presentation">
                <button id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Reviews (<?php echo $product->get_review_count(); ?>)</button>
            </li>
        </ul>
        
        <div class="tab-content" id="product-tab-content">
            <!-- Description Tab -->
            <div class="tab-pane fade show active description" id="home" role="tabpanel" aria-labelledby="home-tab">
                <?php the_content(); ?>
            </div>
            
            <!-- Additional Information Tab -->
            <div class="tab-pane ad-info fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <?php woocommerce_product_additional_information_tab(); ?>
            </div>
            
            <!-- Reviews Tab -->
            <div class="tab-pane fade review" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            <?php comment_form(array('comment_notes_after' => '')); ?>
            </div>
        </div>
    </div>
</section>

<?php
get_footer('shop');
