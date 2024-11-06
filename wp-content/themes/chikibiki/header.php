<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="<?php bloginfo("language") ?>"> <!--<![endif]-->
    
<head>
        <meta charset="<?php bloginfo('charset')?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="<?php bloginfo('description')?>">
        <meta name="author" content="ThemeEaster">

        <title> <?php bloginfo('name') ?> | <?php bloginfo('description') ?> </title>

		<link rel="shortcut icon" type="image/x-icon" href="<?php echo get_template_directory_uri();?>/assets/img/favicon.png">

        <link rel="stylesheet" href="<?php echo get_stylesheet_uri();?>">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/animate.min.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/fontawesome.min.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/line-awesome.min.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/food-icon.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/slider.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/venobox.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/slick.min.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/swiper.min.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/splitting-cells.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/splitting.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/keyframe-animation.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/header.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/blog.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/main.css">
        <link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/assets/css/responsive.css">

        <script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
        <?php wp_head() ?>
    </head>

    <body class="header-1">
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="site-preloader-wrap">
            <div class="spinner"></div>
        </div><!-- /.site-preloader-wrap -->

        <header class="header dark-text">
            <div class="primary-header-one primary-header">
                <div class="container">
                    <div class="primary-header-inner">
                        <div class="header-logo">
                            <a href="<?php bloginfo('wpurl'); ?> ">
                   

                            <?php
                    
                            $custom_logo_id = get_theme_mod( 'custom_logo' );
$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
if ( has_custom_logo() ) {
    echo '<img src="' . esc_url( $logo[0] ) . '" alt="' . get_bloginfo( 'name' ) . '">';
} else {
    echo '<h1>' . get_bloginfo('name') . '</h1>';
} ?>

                            </a>
                  
                        </div><!-- /.header-logo -->
                        <div class="header-menu-wrap">
                            <ul class="slider-menu">
                                <?php $args=array("theme-location"=> 'main-menu') ?>
                                <?php wp_nav_menu($args)  ?>
                            
                            </ul>
                        </div><!-- /.header-menu-wrap -->
                        <div class="d-flex">
    <button class="btn btn-outline-secondary" type="button" onclick="window.location.href='<?php echo wc_get_cart_url(); ?>'">
        <i class="fas fa-shopping-cart"></i> Cart
        <span class="badge bg-danger"><?php echo WC()->cart->get_cart_contents_count(); ?></span> <!-- Dynamic badge for item count -->
    </button>
</div>

                            <!-- Burger menu -->
                            <div class="mobile-menu-icon">
                                <div class="burger-menu">
                                    <div class="line-menu line-half first-line"></div>
                                    <div class="line-menu"></div>
                                    <div class="line-menu line-half last-line"></div>
                                </div>
                            </div>
                        </div><!-- /.header-right -->
                    </div><!-- /.primary-header-one-inner -->
                </div>
            </div><!-- /.primary-header-one -->
        </header><!-- /.header-one -->

        <div id="popup-search-box">
            <div class="box-inner-wrap d-flex align-items-center">
                <form id="form" action="#" method="get" role="search">
                    <input id="popup-search" type="text" name="s" placeholder="Type keywords here..." />
                    <button id="popup-search-button" type="submit" name="submit"><i class="las la-search"></i></button>
                </form>
            </div>
        </div>