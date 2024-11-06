<?php
defined('ABSPATH') || exit;

global $product;

if (empty($product) || !$product->is_visible()) {
    return;
}
?>

<li class="col-lg-4 col-sm-6 padding-15">
    <div class="product-item">
        <?php if ($product->is_on_sale()) : ?>
            <div class="sale"><?php echo esc_html__('-' . round(100 * (1 - $product->get_sale_price() / $product->get_regular_price())) . '%', 'woocommerce'); ?></div>
        <?php endif; ?>

        <div class="product-thumb">
            <a href="<?php the_permalink(); ?>">
                <?php woocommerce_template_loop_product_thumbnail(); ?>
            </a>
            <div>
                <a class="order-btn" href="<?php the_permalink(); ?>"><?php esc_html_e('Order Now', 'woocommerce'); ?></a>
            </div>
        </div>

        <div class="food-info">
            <ul class="ratting">
                <li><?php echo wc_get_product_category_list($product->get_id()); ?></li>
                <?php for ($i = 0; $i < 5; $i++) : ?>
                    <li><i class="las la-star"></i></li>
                <?php endfor; ?>
            </ul>
            <h3><?php the_title(); ?></h3>
            <div class="price">
                <h4>Price: <span><?php echo $product->get_price_html(); ?></span> 
                    <?php if ($product->is_on_sale()) : ?>
                        <span class="reguler"><?php echo wc_price($product->get_regular_price()); ?></span>
                    <?php endif; ?>
                </h4>
            </div>
        </div>
    </div>
</li>
