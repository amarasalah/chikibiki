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
                            <div class="swiper-slide">
                                <div class="food-item">
                                    <div class="food-icon">
                                        <i class="fi fi-pizza-slice"></i>
                                    </div>
                                    <div class="food-content">
                                        <h3>Maxican Pizza</h3>
                                        <p>Food is any substance consumed to provide nutritional support for an organism.</p>
                                    </div>
                                    <div class="food-thumb">
                                        <img src="<?php echo get_template_directory_uri();?>/assets/img/promo01.png" alt="img">
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="food-item">
                                    <div class="food-icon">
                                        <i class="fi fi-beer"></i>
                                    </div>
                                    <div class="food-content">
                                        <h3>Soft Drinks</h3>
                                        <p>Food is any substance consumed to provide nutritional support for an organism.</p>
                                    </div>
                                    <div class="food-thumb">
                                        <img src="<?php echo get_template_directory_uri();?>/assets/img/promo02.png" alt="img">
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="food-item">
                                    <div class="food-icon">
                                        <i class="fi fi-fried-potatoes"></i>
                                    </div>
                                    <div class="food-content">
                                        <h3>French Fry</h3>
                                        <p>Food is any substance consumed to provide nutritional support for an organism.</p>
                                    </div>
                                    <div class="food-thumb">
                                        <img src="<?php echo get_template_directory_uri();?>/assets/img/promo03.png" alt="img">
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="food-item">
                                    <div class="food-icon">
                                        <i class="fi fi-burger"></i>
                                    </div>
                                    <div class="food-content">
                                        <h3>Burger Kingo</h3>
                                        <p>Food is any substance consumed to provide nutritional support for an organism.</p>
                                    </div>
                                    <div class="food-thumb">
                                        <img src="<?php echo get_template_directory_uri();?>/assets/img/promo04.png" alt="img">
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="food-item">
                                    <div class="food-icon">
                                        <i class="fi fi-chicken-leg"></i>
                                    </div>
                                    <div class="food-content">
                                        <h3>Chicken Masala</h3>
                                        <p>Food is any substance consumed to provide nutritional support for an organism.</p>
                                    </div>
                                    <div class="food-thumb">
                                        <img src="<?php echo get_template_directory_uri();?>/assets/img/promo05.png" alt="img">
                                    </div>
                                </div>
                            </div>
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
        </section><!--/.promo-section-->

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
                <ul class="food-menu-filter">
                    <li class="active" data-filter="*">All</li>
                    <li data-filter=".pizza">Pizza</li>
                    <li data-filter=".burger">Burger</li>
                    <li data-filter=".drinks">Drinks</li>
                    <li data-filter=".sandwich">Sandwich</li>
                </ul>
                <div class="row product-items">
                    <div class="col-lg-4 col-md-6 padding-15 isotop-grid pizza sandwich">
                        <div class="product-item wow fadeInUp" data-wow-delay="200ms">
                            <div class="sale">-15%</div>
                            <div class="product-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/food01.png" alt="food">
                                <div><a class='order-btn' href='shop-details.html'>Order Now</a></div>
                            </div>
                            <div class="food-info">
                                <ul class="ratting">
                                    <li>Chicken</li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                </ul>
                                <h3>Fried Chicken Unlimited</h3>
                                <div class="price">
                                    <h4>Price: <span>$49.00</span> <span class="reguler">$69.00</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 padding-15 isotop-grid burger sandwich">
                        <div class="product-item wow fadeInUp" data-wow-delay="400ms">
                            <div class="sale">-10%</div>
                            <div class="product-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/food02.png" alt="food">
                                <div><a class='order-btn' href='shop-details.html'>Order Now</a></div>
                            </div>
                            <div class="food-info">
                                <ul class="ratting">
                                    <li>Noddles</li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                </ul>
                                <h3>Burger King Whopper</h3>
                                <div class="price">
                                    <h4>Price: <span>$29.00</span> <span class="reguler">$39.00</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 padding-15 isotop-grid drinks burger">
                        <div class="product-item wow fadeInUp" data-wow-delay="600ms">
                            <div class="sale">-25%</div>
                            <div class="product-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/food03.png" alt="food">
                                <div><a class='order-btn' href='shop-details.html'>Order Now</a></div>
                            </div>
                            <div class="food-info">
                                <ul class="ratting">
                                    <li>Pizzas</li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                </ul>
                                <h3>White Castle Pizzas</h3>
                                <div class="price">
                                    <h4>Price: <span>$49.00</span> <span class="reguler">$69.00</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 padding-15 isotop-grid sandwich drinks">
                        <div class="product-item wow fadeInUp" data-wow-delay="200ms">
                            <div class="sale">-20%</div>
                            <div class="product-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/food04.png" alt="food">
                                <div><a class='order-btn' href='shop-details.html'>Order Now</a></div>
                            </div>
                            <div class="food-info">
                                <ul class="ratting">
                                    <li>Burrito</li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                </ul>
                                <h3>Bell Burrito Supreme</h3>
                                <div class="price">
                                    <h4>Price: <span>$59.00</span> <span class="reguler">$69.00</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 padding-15 isotop-grid burger drinks">
                        <div class="product-item wow fadeInUp" data-wow-delay="400ms">
                            <div class="sale">-5%</div>
                            <div class="product-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/food05.png" alt="food">
                                <div><a class='order-btn' href='shop-details.html'>Order Now</a></div>
                            </div>
                            <div class="food-info">
                                <ul class="ratting">
                                    <li>Nuggets</li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                </ul>
                                <h3>Kung Pao Chicken BBQ</h3>
                                <div class="price">
                                    <h4>Price: <span>$49.00</span> <span class="reguler">$69.00</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 padding-15 isotop-grid sandwich pizza">
                        <div class="product-item wow fadeInUp" data-wow-delay="600ms">
                            <div class="sale">-15%</div>
                            <div class="product-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/food06.png" alt="food">
                                <div><a class='order-btn' href='shop-details.html'>Order Now</a></div>
                            </div>
                            <div class="food-info">
                                <ul class="ratting">
                                    <li>Chicken</li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                    <li><i class="las la-star"></i></li>
                                </ul>
                                <h3>Wendy's Chicken</h3>
                                <div class="price">
                                    <h4>Price: <span>$49.00</span> <span class="reguler">$69.00</span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section><!--/.food-menu-->



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
                    <div class="col-md-6 wow fadeInUp" data-wow-delay="200ms">
                        <div class="banner-item">
                            <img src="<?php echo get_template_directory_uri();?>/assets/img/banner01.jpg" alt="banner">
                            <div class="banner-content">
                                <h3>-50% Off Now!</h3>
                                <h2>Discount For Delicious <br>Tasty Burgers!</h2>
                                <p>Sale off 50% only this week</p>
                                <a class='order-btn' href='shop.html'>Order Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6 wow fadeInUp" data-wow-delay="400ms">
                                <div class="banner-item">
                                    <img src="<?php echo get_template_directory_uri();?>/assets/img/banner02.jpg" alt="banner">
                                    <div class="banner-content">
                                        <h3>Delicious <br> Pizza</h3>
                                        <p>50% off Now</p>
                                        <a class='order-btn' href='shop.html'>Order Now</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 wow fadeInUp" data-wow-delay="600ms">
                                <div class="banner-item">
                                    <img src="<?php echo get_template_directory_uri();?>/assets/img/banner03.jpg" alt="banner">
                                    <div class="banner-content">
                                        <h3>American <br>Burgers</h3>
                                        <p>50% off Now</p>
                                        <a class='order-btn' href='shop.html'>Order Now</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 wow fadeInUp" data-wow-delay="800ms">
                                <div class="banner-item">
                                    <img src="<?php echo get_template_directory_uri();?>/assets/img/banner04.jpg" alt="banner">
                                    <div class="banner-content">
                                        <h3>Tasty Buzzed <br>Pizza</h3>
                                        <p>Sale off 50% only this week</p>
                                        <a class='order-btn' href='shop.html'>Order Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!--/.banner-section-->
        <section class="team-section padding">
           <div class="bg-shape grey"></div>
            <div class="container">
                <div class="section-heading mb-30 text-center wow fadeInUp" data-wow-delay="200ms">
                    <h4>Team Mebmers</h4>
                    <h2>Our Expart <span>Chefs</span></h2>
                    <p>Food is any substance consumed to provide nutritional <br> support for an organism.</p>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-sm-6 sm-padding wow fadeInUp" data-wow-delay="200ms">
                        <div class="team-item">
                            <div class="team-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/team-01.jpg" alt="team">
                                <ul class="team-social">
                                    <li><a href="#"><i class="lab la-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="lab la-twitter"></i></a></li>
                                    <li><a href="#"><i class="lab la-instagram"></i></a></li>
                                    <li><a href="#"><i class="lab la-behance"></i></a></li>
                                </ul>
                            </div>
                            <div class="team-content">
                                <div class="team-shape"></div>
                                <div class="inner">
                                    <h3>Charles Richard</h3>
                                    <h4>Executive Chef</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 sm-padding wow fadeInUp" data-wow-delay="400ms">
                        <div class="team-item">
                            <div class="team-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/team-02.jpg" alt="team">
                                <ul class="team-social">
                                    <li><a href="#"><i class="lab la-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="lab la-twitter"></i></a></li>
                                    <li><a href="#"><i class="lab la-instagram"></i></a></li>
                                    <li><a href="#"><i class="lab la-behance"></i></a></li>
                                </ul>
                            </div>
                            <div class="team-content">
                                <div class="team-shape"></div>
                                <div class="inner">
                                    <h3>Robert William</h3>
                                    <h4>Head Chef</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 sm-padding wow fadeInUp" data-wow-delay="600ms">
                        <div class="team-item">
                            <div class="team-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/team-03.jpg" alt="team">
                                <ul class="team-social">
                                    <li><a href="#"><i class="lab la-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="lab la-twitter"></i></a></li>
                                    <li><a href="#"><i class="lab la-instagram"></i></a></li>
                                    <li><a href="#"><i class="lab la-behance"></i></a></li>
                                </ul>
                            </div>
                            <div class="team-content">
                                <div class="team-shape"></div>
                                <div class="inner">
                                    <h3>Thomas Josef</h3>
                                    <h4>Junior Chef</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 sm-padding wow fadeInUp" data-wow-delay="800ms">
                        <div class="team-item">
                            <div class="team-thumb">
                                <img src="<?php echo get_template_directory_uri();?>/assets/img/team-04.jpg" alt="team">
                                <ul class="team-social">
                                    <li><a href="#"><i class="lab la-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="lab la-twitter"></i></a></li>
                                    <li><a href="#"><i class="lab la-instagram"></i></a></li>
                                    <li><a href="#"><i class="lab la-behance"></i></a></li>
                                </ul>
                            </div>
                            <div class="team-content">
                                <div class="team-shape"></div>
                                <div class="inner">
                                    <h3>Mike Albatson</h3>
                                    <h4>Kitchen Porter</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!--/.team-section-->

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
                            <div class="swiper-slide">
                                <div class="testimonial-item">
                                    <div class="testi-thumb">
                                        <img src="<?php echo get_template_directory_uri();?>/assets/img/testi01.jpg" alt="img">
                                        <div class="author">
                                            <h3>Robert William</h3>
                                            <h4>CEO Kingfisher</h4>
                                        </div>
                                    </div>
                                    <p> "I would be lost without restaurant. I would like to personally thank you for your outstanding product."</p>
                                    <ul class="ratting">
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="testimonial-item">
                                    <div class="testi-thumb">
                                        <img src="<?php echo get_template_directory_uri();?>/assets/img/testi02.jpg" alt="img">
                                        <div class="author">
                                            <h3>Thomas Josef</h3>
                                            <h4>CEO Getforce</h4>
                                        </div>
                                    </div>
                                    <p> "I would be lost without restaurant. I would like to personally thank you for your outstanding product."</p>
                                    <ul class="ratting">
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="testimonial-item">
                                    <div class="testi-thumb">
                                        <img src="<?php echo get_template_directory_uri();?>/assets/img/testi03.jpg" alt="img">
                                        <div class="author">
                                            <h3>Charles Richard</h3>
                                            <h4>CEO Angela</h4>
                                        </div>
                                    </div>
                                    <p> "I would be lost without restaurant. I would like to personally thank you for your outstanding product."</p>
                                    <ul class="ratting">
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                        <li><i class="las la-star"></i></li>
                                    </ul>
                                </div>
                            </div>
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
        </section><!--/.testimonial-section-->



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