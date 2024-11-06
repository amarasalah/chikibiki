
<footer class="footer-section">
            <div class="footer-top">
                <div class="footer-illustration"></div>
                <div class="running-cycle"><div></div></div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 sm-padding">
                            <div class="footer-widget">
                                <a class="logo" href="<?php bloginfo('wpurl'); ?> "><img src="<?php echo get_template_directory_uri();?>/assets/img/logo-dark.png" alt="img"></a>
                                <p>Financial experts support or help you to to find out which way you can raise your funds more.</p>
                                <ul class="footer-social">
                                    <li><a href="#"><i class="fab fa-facebook"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="#"><i class="fab fa-pinterest"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 sm-padding">
                            <?php if (is_active_sidebar("footer-1")): ?>
                            <?php dynamic_sidebar("footer-1") ?>
                            <?php endif; ?>

                        </div>
                        <div class="col-lg-3 col-sm-6 sm-padding">
                            <div class="footer-widget ml-25">
                                <h3>Opening Hours <span></span></h3>
                                <ul class="opening-hours-list">
                                    <li>Monday-Friday: 08:00-22:00</li>
                                    <li>Tuesday4PM:  Till Mid Night</li>
                                    <li>Saturday: 10:00-16:00</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 sm-padding">
                            <div class="footer-widget booking-form">
                                <h3>Book a Table <span></span></h3>
                                <form action="https://te.dynamiclayers.net/caferio/booking-form.php" method="post" id="ajax_booking_form" class="form-horizontal">
                                    <div class="booking-form-group">
                                        <div class="form-padding">
                                            <input type="text" id="b_name" name="b_name" class="form-control" placeholder="Your Name" required>
                                        </div>
                                        <div class="form-padding">
                                            <input type="email" id="b_email" name="b_email" class="form-control" placeholder="Email" required>
                                        </div>
                                        <div class="form-padding">
                                            <select class="form-select" id="b_person" name="b_person">
                                              <option selected>Person</option>
                                              <option>2 Person</option>
                                              <option>3 Person</option>
                                              <option>4 Person</option>
                                              <option>5 Person</option>
                                            </select>
                                        </div>
                                        <div class="form-padding">
                                            <input class="form-control" type="date" id="b_date" name="b_date">
                                        </div>
                                        <div class="form-padding">
                                            <textarea id="b_message" name="b_message" cols="30" rows="5" class="form-control message" placeholder="Message" required></textarea>
                                        </div>
                                    </div>
                                    <button id="b_submit" class="book-btn" type="submit">Book a Table</button>
                                    <div id="b-form-messages" class="alert" role="alert"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--/.footer-top -->
            <div class="footer-bottom">
                <div class="container">
                    <div class="copyright-wrap">
                        <p>© <span id="currentYear"></span> Chiki Bikic All Rights Reserved.</p>
                    </div>
                </div>
            </div><!--/.footer-bottom -->
        </footer><!--/.footer-section -->

		<div id="scrollup">
            <button id="scroll-top" class="scroll-to-top"><i class="las la-arrow-up"></i></button>
        </div>

		<!-- jQuery Lib -->
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/jquery-3.5.1.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/bootstrap.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/imagesloaded.pkgd.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/jquery.isotope.v3.0.2.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/splitting.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/slick.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/swiper.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/venobox.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/simpleParallax.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/smooth-scroll.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/waypoints.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/vendor/wow.min.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/booking.js"></script>
		<script src="<?php echo get_template_directory_uri();?>/assets/js/main.js"></script>
        <?php wp_footer() ?>
    </body>

</html>
