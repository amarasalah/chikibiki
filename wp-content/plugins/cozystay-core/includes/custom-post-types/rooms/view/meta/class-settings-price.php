<?php
namespace LoftOcean\Room\Settings;

if ( ! class_exists( '\LoftOcean\Room\Settings\Price' ) ) {
	class Price {
		/*
		* Default variable settings
		*/
		protected $variable_default_values = array();
		/**
		* Construct function
		*/
		public function __construct() {
			$this->variable_default_values = array(
				'nightly' => array_fill_keys( array( 'guest_number', 'adult_number', 'child_number', 'price', 'weekend_price' ), '' ),
				'per_person' => array_fill_keys( array( 'guest_number', 'adult_number', 'child_number', 'price', 'adult_price', 'child_price', 'weekend_price', 'weekend_adult_price', 'weekend_child_price' ), '' )
			);


			add_action( 'loftocean_room_the_settings_tabs', array( $this, 'get_room_setting_tabs' ) );
			add_action( 'loftocean_room_the_settings_panel', array( $this, 'the_room_setting_panel' ) );
			add_action( 'loftocean_save_room_settings', array( $this, 'save_room_settings' ) );
		}
		/**
		* Tab titles
		*/
		public function get_room_setting_tabs( $pid ) { ?>
			<li class="loftocean-room room-options tab-price">
				<a href="#tab-price"><span><?php esc_html_e( 'Price & Capacity', 'loftocean' ); ?></span></a>
			</li><?php
		}
		/**
		* Tab panel
		*/
		public function the_room_setting_panel( $pid ) {
			$data = $this->get_room_data( $pid );
			$enable_max_child_number = isset( $data[ 'room_enable_max_child_number' ] ) && ( 'on' == $data[ 'room_enable_max_child_number' ] ) ? 'on' : '';
			$max_child_number = isset( $data[ 'room_max_child_number' ] ) && is_numeric( $data[ 'room_max_child_number' ] ) ? $data[ 'room_max_child_number' ] : '';
			$enable_max_adult_number = isset( $data[ 'room_enable_max_adult_number' ] ) && ( 'on' == $data[ 'room_enable_max_adult_number' ] ) ? 'on' : '';
			$max_adult_number = isset( $data[ 'room_max_adult_number' ] ) && is_numeric( $data[ 'room_max_adult_number' ] ) ? $data[ 'room_max_adult_number' ] : '';
			$enable_price_by_people = isset( $data[ 'room_price_by_people' ] ) && ( 'on' == $data[ 'room_price_by_people' ] ) ? 'on' : '';
			$enable_weekend_prices = isset( $data[ 'room_enable_weekend_prices' ] ) && ( 'on' == $data[ 'room_enable_weekend_prices' ] ) ? 'on' : '';
			$enable_variable_prices = isset( $data[ 'room_enable_variable_prices' ] ) && ( 'on' == $data[ 'room_enable_variable_prices' ] ) ? 'on' : '';
			$enable_guest_group = isset( $data[ 'room_enable_variable_guest_group' ] ) && ( 'on' == $data[ 'room_enable_variable_guest_group' ] ) ? 'on' : '';
			$enable_variable_weekend_prices = isset( $data[ 'room_enable_variable_weekend_prices' ] ) && ( 'on' == $data[ 'room_enable_variable_weekend_prices' ] ) ? 'on' : '';
			$nightly_variable_prices = isset( $data[ 'room_variable_nightly_prices' ] ) ? $data[ 'room_variable_nightly_prices' ] : array( array() );
			$per_person_variable_prices = isset( $data[ 'room_variable_per_person_prices' ] ) ? $data[ 'room_variable_per_person_prices' ] : array( array() );
			$variable_prices_wrap_class = array( 'loftocean-variable-price' );
			empty( $enable_guest_group ) ? '' : array_push( $variable_prices_wrap_class, 'has-guest-group' );
			empty( $enable_variable_weekend_prices ) ? '' : array_push( $variable_prices_wrap_class, 'has-weekend-price' ); ?>

			<div id="tab-price-panel" class="panel loftocean-room-setting-panel hidden">
				<div class="options-group">
					<h5 class="option-title"><?php esc_html_e( 'Regular Price & Capacity', 'loftocean' ); ?></h5>
					<p class="form-field number-field">
						<label for="room_regular_price"><?php esc_html_e( 'Regular Price (Per Night)', 'loftocean' ); ?></label>
						<input name="loftocean_room_regular_price" id="room_regular_price" value="<?php echo esc_attr( $data['room_regular_price'] ); ?>" type="number" step="0.01" />
					</p>
					<div class="form-field capacity-field">
                        <label for="room_capacity"><?php esc_html_e( 'Number of Guests', 'loftocean' ); ?></label>

                        <div class="multi-items-wrapper">
                            <input name="loftocean_room_min_people" id="room_min_people" type="number" placeholder="<?php esc_attr_e( 'Min People', 'loftocean' ); ?>" value="<?php echo esc_attr( $data['room_min_people'] ); ?>">
                            <span>-</span>
                            <input name="loftocean_room_max_people" id="room_max_people" type="number" placeholder="<?php esc_attr_e( 'Max People', 'loftocean' ); ?>" value="<?php echo esc_attr( $data['room_max_people'] ); ?>">
                        </div>
                    </div>
                    <p class="form-field checkbox-field">
                        <label for="room_max_adults"><?php esc_html_e( 'Max Adults', 'loftocean' ); ?></label>
                        <input id="room_max_adults" type="checkbox" name="loftocean_room_enable_max_adult_number" value="on" <?php checked( 'on', $enable_max_adult_number ); ?>>
                        <span class="trigger-label"><?php esc_html_e( 'Limit the number of adults in this room no more than', 'loftocean' ); ?></span>
                        <input type="number" name="loftocean_room_max_adult_number" value="<?php echo esc_attr( $max_adult_number ); ?>" class="small-text" min=0>
                    </p>
                    <p class="form-field checkbox-field">
                        <label for="room_max_children"><?php esc_html_e( 'Max Children', 'loftocean' ); ?></label>
                        <input id="room_max_children" type="checkbox" name="loftocean_room_enable_max_child_number" value="on" <?php checked( 'on', $enable_max_child_number ); ?>>
                        <span class="trigger-label"><?php esc_html_e( 'Limit the number of children in this room no more than', 'loftocean' ); ?></span>
                        <input type="number" name="loftocean_room_max_child_number" value="<?php echo esc_attr( $max_child_number ); ?>" class="small-text" min=0>
                    </p>
                    <hr>

                    <div class="option-group">
                        <div class="option-title-with-toggle checkbox-field">
                            <input name="loftocean_room_price_by_people" id="room_price_by_people" type="checkbox" value="on" <?php checked( $enable_price_by_people, 'on' ); ?>>
                            <label for="room_price_by_people"><?php esc_html_e( 'Set price per person per night', 'loftocean' ); ?></label>
                        </div>
                        <div class="option-content-after-toggle"<?php if ( 'on' != $enable_price_by_people ) : ?> style="display: none;"<?php endif; ?>>
                            <p class="description"><?php esc_html_e( 'By activating this option, the nightly price for this room will be the price per person per night multiplied by the number of guests.', 'loftocean' ); ?></p>
                            <p class="form-field number-field price-by-people-unit">
                                <label for="room_price_per_adult"><?php esc_html_e( 'Adult Price', 'loftocean' ); ?></label>
                                <input name="loftocean_room_price_per_adult" id="room_price_per_adult" value="<?php echo esc_attr( $data['room_price_per_adult'] ); ?>" type="number" step="0.01" > 
                                <?php esc_html_e( 'Per Adult', 'loftocean' ); ?>
                            </p>
                            <p class="form-field number-field price-by-people-unit">
                                <label for="room_price_per_child"><?php esc_html_e( 'Children Price', 'loftocean' ); ?></label>
                                <input name="loftocean_room_price_per_child" id="room_price_per_child" value="<?php echo esc_attr( $data['room_price_per_child'] ); ?>" type="number" step="0.01"> 
                                <?php esc_html_e( 'Per Child', 'loftocean' ); ?>
                            </p>
                        </div>
                    </div>
                    <hr>

                    <div class="option-group">
                        <div class="option-title-with-toggle checkbox-field">
                            <input name="loftocean_room_enable_weekend_prices" id="room_enable_weekend_prices" type="checkbox" value="on"<?php checked( 'on', $enable_weekend_prices ); ?>>
                            <label for="room_enable_weekend_prices"><?php esc_html_e( 'Weekend Pricing', 'loftocean' ); ?></label>
                        </div>

                        <div class="option-content-after-toggle"<?php if ( 'on' != $enable_weekend_prices ) : ?> style="display: none;"<?php endif; ?>>
                            <p class="description"><?php printf(
								// translators: 1/2 html tag
								esc_html__( 'This will replace the base price for every Friday and Saturday. %1$sModify weekend days?%2$s', 'loftocean' ),
								'<a href="' . admin_url( 'edit.php?post_type=loftocean_room&page=loftocean_room_general_settings' ) . '" target="_blank">',
								'</a>'
							); ?></p>

                            <p class="form-field number-field weekend-prices nightly-weekend-price<?php if ( 'on' == $enable_price_by_people ) : ?> hide<?php endif; ?>">
                                <label for="room_weekend_price_per_night">Weekend Price</label>
                                <input name="loftocean_room_weekend_price_per_night" id="room_weekend_price_per_night" value="<?php echo esc_attr( $data['room_weekend_price_per_night'] ); ?>" type="number" step="0.01"> 
                                <?php esc_html_e( 'Per Night', 'loftocean' ); ?>
                            </p>

                            <p class="form-field number-field weekend-prices per-person-weekend-price<?php if ( 'on' != $enable_price_by_people ) : ?> hide<?php endif; ?>">
                                <label for="room_weekend_price_per_adult">Adult Weekend Price</label>
                                <input name="loftocean_room_weekend_price_per_adult" id="room_weekend_price_per_adult" value="<?php echo esc_attr( $data['room_weekend_price_per_adult'] ); ?>" type="number" step="0.01"> 
                                <?php esc_html_e( 'Per Adult', 'loftocean' ); ?>
                            </p>

                            <p class="form-field number-field weekend-prices per-person-weekend-price<?php if ( 'on' != $enable_price_by_people ) : ?> hide<?php endif; ?>">
                                <label for="room_weekend_price_per_child">Child Weekend Price</label>
                                <input name="loftocean_room_weekend_price_per_child" id="room_weekend_price_per_child" value="<?php echo esc_attr( $data['room_weekend_price_per_child'] ); ?>" type="number" step="0.01"> 
                                <?php esc_html_e( 'Per Child', 'loftocean' ); ?>
                            </p>
                        </div>
                    </div>
                    <hr>

                    <div class="option-group">
                        <div class="option-title-with-toggle checkbox-field">
                            <input name="loftocean_room_enable_variable_prices" id="room_enable_variable_prices" type="checkbox" value="on" <?php checked( 'on', $enable_variable_prices ); ?>>
                            <label for="room_enable_variable_prices"><?php esc_html_e( 'Variable Pricing', 'loftocean' ); ?></label>
                        </div>

                        <div class="option-content-after-toggle"<?php if ( 'on' != $enable_variable_prices ) : ?> style="display: none;"<?php endif; ?>>
                            <p class="description"><?php printf(
                            	// translators: 1/2 html tag
                            	esc_html__( 'You can set different prices for the room depending on the number of guests staying in the room. %1$sRead documentation on this feature%2$s.', 'loftocean' ),
                            	'<br><a href="https://loftocean.com/doc/cozystay/ptkb/variable-pricing/" target="_blank">',
                            	'</a>'
                            ); ?></p>

                            <p class="form-field checkbox-field">
                                <label for="room_enable_variable_guest_group"><?php esc_html_e( 'Guest Group', 'loftocean' ); ?></label>
                                <input name="loftocean_room_enable_variable_guest_group" id="room_enable_variable_guest_group" type="checkbox" value="on" <?php checked( 'on', $enable_guest_group ); ?>>
                                <span class="trigger-label"><?php esc_html_e( 'Separate settings for Adults and Children', 'loftocean' ); ?></span>
                            </p>

                            <p class="form-field checkbox-field">
                                <label for="room_enable_variable_weekend_prices"><?php esc_html_e( 'Weekend Pricing', 'loftocean' ); ?></label>
                                <input name="loftocean_room_enable_variable_weekend_prices" id="room_enable_variable_weekend_prices" type="checkbox" value="on" <?php checked( 'on', $enable_variable_weekend_prices ); ?>>
                                <span class="trigger-label"><?php esc_html_e( 'Set variable prices for weekends', 'loftocean' ); ?></span>
                            </p>


                            <div class="variable-price-by-room <?php echo esc_attr( implode( ' ', $variable_prices_wrap_class ) ); if ( 'on' == $enable_price_by_people ) : ?> hide<?php endif; ?>">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="variable-guests-number"><?php esc_html_e( 'Number of Guests', 'loftocean' ); ?></th>
                                            <th class="variable-adults-number"><?php esc_html_e( 'Adults', 'loftocean' ); ?></th>
                                            <th class="variable-children-number"><?php esc_html_e( 'Children', 'loftocean' ); ?></th>
                                            <th class="variable-regular-price"><?php esc_html_e( 'Regular Price (Per Night)', 'loftocean' ); ?></th>
                                            <th class="variable-weekend-price"><?php esc_html_e( 'Weekend Price (Per Night)', 'loftocean' ); ?></th>
                                            <th class="variable-actions"></th>
                                        </tr>
                                    </thead>
                                    <tbody><?php
                                    	$nightly_variable_prices_index = 1;
                                        $default_value = $this->variable_default_values[ 'nightly' ];
                                        $nightly_variable_prices = \LoftOcean\is_valid_array( $nightly_variable_prices ) ? $nightly_variable_prices : array( $default_value );
                                		$nightly_variable_prices_length = count( $nightly_variable_prices );
                                		$option_name_prefix = 'loftocean_room_variable_nightly_prices';
										foreach ( $nightly_variable_prices as $nvp ) : 
											$current_name_prefix = sprintf( '%1$s[%2$s]', $option_name_prefix, $nightly_variable_prices_index ++ );
											$nvp = array_merge( $default_value, $nvp ); ?>
											<tr>
												<td class="variable-guests-number">
													<input name="<?php echo $current_name_prefix ; ?>[guest_number]" value="<?php echo esc_attr( $nvp[ 'guest_number' ] ); ?>" type="number" min=0>
												</td>

												<td class="variable-adults-number">
													<input name="<?php echo $current_name_prefix ; ?>[adult_number]" value="<?php echo esc_attr( $nvp[ 'adult_number' ] ); ?>" type="number" min=0>
												</td>

												<td class="variable-children-number">
													<input name="<?php echo $current_name_prefix ; ?>[child_number]" value="<?php echo esc_attr( $nvp[ 'child_number' ] ); ?>" type="number" min=0>
												</td>

												<td class="variable-regular-price">
													<input name="<?php echo $current_name_prefix ; ?>[price]" value="<?php echo esc_attr( $nvp[ 'price' ] ); ?>" type="number" step="0.01" min=0>
												</td>

												<td class="variable-weekend-price">
													<input name="<?php echo $current_name_prefix ; ?>[weekend_price]" value="<?php echo esc_attr( $nvp[ 'weekend_price' ] ); ?>" type="number" step="0.01" min=0>
												</td>

												<td class="variable-actions">
													<button class="button"><?php esc_html_e( 'Delete', 'loftocean' ); ?></button>
												</td>
											</tr><?php
	                                    endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr><td colspan="6">
                                        	<button class="button" data-current-index="<?php echo $nightly_variable_prices_index; ?>" data-current-type="regular"><?php esc_html_e( 'Add New', 'loftocean' ); ?></button>
                                        </td></tr>
                                    </tfoot>
                                    
                                </table>
                            </div>

                            <div class="variable-price-by-guest <?php echo esc_attr( implode( ' ', $variable_prices_wrap_class ) ); if ( 'on' != $enable_price_by_people ) : ?> hide<?php endif; ?>">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="variable-guests-number"><?php esc_html_e( 'Number of Guests', 'loftocean' ); ?></th>
                                            <th class="variable-adults-number"><?php esc_html_e( 'Adults', 'loftocean' ); ?></th>
                                            <th class="variable-children-number"><?php esc_html_e( 'Children', 'loftocean' ); ?></th>
                                            <th class="variable-regular-price"><?php esc_html_e( 'Regular Price (Per Guest)', 'loftocean' ); ?></th>
                                            <th class="variable-regular-price-adult"><?php esc_html_e( 'Price Per Adult', 'loftocean' ); ?></th>
                                            <th class="variable-regular-price-child"><?php esc_html_e( 'Price Per Child', 'loftocean' ); ?></th>
                                            <th class="variable-weekend-price"><?php esc_html_e( 'Weekend Price (Per Guest)', 'loftocean' ); ?></th>
                                            <th class="variable-weekend-price-adult"><?php esc_html_e( 'Weekend Price / Adult', 'loftocean' ); ?></th>
                                            <th class="variable-weekend-price-child"><?php esc_html_e( 'Weekend Price / Child', 'loftocean' ); ?></th>
                                            <th class="variable-actions"></th>
                                        </tr>
                                    </thead>
                                    <tbody><?php
                                    	$per_person_variable_prices_index = 1;
                                        $default_value = $this->variable_default_values[ 'per_person' ];
                                    	$per_person_variable_prices = \LoftOcean\is_valid_array( $per_person_variable_prices ) ? $per_person_variable_prices : array( $default_value );
                                		$option_name_prefix = 'loftocean_room_variable_per_person_prices';
                                		foreach ( $per_person_variable_prices as $ppvp ) :
                                			$current_name_prefix = sprintf( '%1$s[%2$s]', $option_name_prefix, $per_person_variable_prices_index ++ );
                                			$ppvp = array_merge( $default_value, $ppvp ); ?>
											<tr>
												<td class="variable-guests-number">
													<input name="<?php echo $current_name_prefix ; ?>[guest_number]" value="<?php echo esc_attr( $ppvp[ 'guest_number' ] ); ?>" type="number" min=0>
												</td>

												<td class="variable-adults-number">
													<input name="<?php echo $current_name_prefix ; ?>[adult_number]" value="<?php echo esc_attr( $ppvp[ 'adult_number' ] ); ?>" type="number" min=0>
												</td>

												<td class="variable-children-number">
													<input name="<?php echo $current_name_prefix ; ?>[child_number]" value="<?php echo esc_attr( $ppvp[ 'child_number' ] ); ?>" type="number" min=0>
												</td>

												<td class="variable-regular-price">
													<input name="<?php echo $current_name_prefix ; ?>[price]" value="<?php echo esc_attr( $ppvp[ 'price' ] ); ?>" type="number" step="0.01" min=0>
												</td>

												<td class="variable-regular-price-adult">
													<input name="<?php echo $current_name_prefix ; ?>[adult_price]" value="<?php echo esc_attr( $ppvp[ 'adult_price' ] ); ?>" type="number" step="0.01" min=0>
												</td>

												<td class="variable-regular-price-child">
													<input name="<?php echo $current_name_prefix ; ?>[child_price]" value="<?php echo esc_attr( $ppvp[ 'child_price' ] ); ?>" type="number" step="0.01" min=0>
												</td>

												<td class="variable-weekend-price">
													<input name="<?php echo $current_name_prefix ; ?>[weekend_price]" value="<?php echo esc_attr( $ppvp[ 'weekend_price' ] ); ?>" type="number" step="0.01" min=0>
												</td>

												<td class="variable-weekend-price-adult">
													<input name="<?php echo $current_name_prefix ; ?>[weekend_adult_price]" value="<?php echo esc_attr( $ppvp[ 'weekend_adult_price' ] ); ?>" type="number" step="0.01" min=0>
												</td>

												<td class="variable-weekend-price-child">
													<input name="<?php echo $current_name_prefix ; ?>[weekend_child_price]" value="<?php echo esc_attr( $ppvp[ 'weekend_child_price' ] ); ?>" type="number" step="0.01" min=0>
												</td>

												<td class="variable-actions">
													<button class="button"><?php esc_html_e( 'Delete', 'loftocean' ); ?></button>
												</td>
											</tr><?php
	                                    endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr><td colspan="10">
                                        	<button class="button" data-current-index="<?php echo $per_person_variable_prices_index; ?>" data-current-type="per-person"><?php esc_html_e( 'Add New', 'loftocean' ); ?></button>
                                        </td></tr>
                                    </tfoot>
                                </table>
                            </div>

                            <p class="description"><?php printf( 
                            	// translators: 1/2 html tag
                            	esc_html__( '%1$sPlease note:%2$s Variable prices will NOT be shown in the calendar of the Availability tab.', 'loftocean' ),
                            	'<strong>',
                            	'</strong>'
                            ); ?></p>
                        </div>
                    </div>
				</div>
				<script id="tmpl-loftocean-room-variable-prices" type="text/template"><#
					if ( data.type && data.index ) {
						var currentIndex = data.index;
						if ( 'regular' == data.type ) {
							var namePrefix = 'loftocean_room_variable_nightly_prices[' + currentIndex + ']'; #>
							<tr>
	                            <td class="variable-guests-number">
	                                <input name="{{{ namePrefix }}}[guest_number]" value="" type="number" min=0>
	                            </td>

	                            <td class="variable-adults-number">
	                                <input name="{{{ namePrefix }}}[adult_number]" value="" type="number" min=0>
	                            </td>

	                            <td class="variable-children-number">
	                                <input name="{{{ namePrefix }}}[child_number]" value="" type="number" min=0>
	                            </td>

	                            <td class="variable-regular-price">
	                                <input name="{{{ namePrefix }}}[price]" value="" type="number" step="0.01" min=0>
	                            </td>

	                            <td class="variable-weekend-price">
	                                <input name="{{{ namePrefix }}}[weekend_price]" value="" type="number" step="0.01" min=0>
	                            </td>

	                            <td class="variable-actions">
	                                <button class="button"><?php esc_html_e( 'Delete', 'loftocean' ); ?></button>
	                            </td>
	                        </tr><#
						} else {
							var namePrefix = 'loftocean_room_variable_per_person_prices[' + currentIndex + ']'; #>
							<tr>
		                        <td class="variable-guests-number">
		                            <input name="{{{ namePrefix }}}[guest_number]" value="" type="number" min=0>
		                        </td>

		                        <td class="variable-adults-number">
		                            <input name="{{{ namePrefix }}}[adult_number]" value="" type="number" min=0>
		                        </td>

		                        <td class="variable-children-number">
		                            <input name="{{{ namePrefix }}}[child_number]" value="" type="number" min=0>
		                        </td>

		                        <td class="variable-regular-price">
		                            <input name="{{{ namePrefix }}}[price]" value="" type="number" step="0.01" min=0>
		                        </td>

		                        <td class="variable-regular-price-adult">
		                            <input name="{{{ namePrefix }}}[adult_price]" value="" type="number" step="0.01" min=0>
		                        </td>

		                        <td class="variable-regular-price-child">
		                            <input name="{{{ namePrefix }}}[child_price]" value="" type="number" step="0.01" min=0>
		                        </td>

		                        <td class="variable-weekend-price">
		                            <input name="{{{ namePrefix }}}[weekend_price]" value="" type="number" step="0.01" min=0>
		                        </td>

		                        <td class="variable-weekend-price-adult">
		                            <input name="{{{ namePrefix }}}[weekend_adult_price]" value="" type="number" step="0.01" min=0>
		                        </td>

		                        <td class="variable-weekend-price-child">
		                            <input name="{{{ namePrefix }}}[weekend_child_price]" value="" type="number" step="0.01" min=0>
		                        </td>

		                        <td class="variable-actions">
		                            <button class="button"><?php esc_html_e( 'Delete', 'loftocean' ); ?></button>
		                        </td>
		                    </tr><#
						}
					} #>
				</script>
			</div><?php
		}
		/**
		* Get room data
		*/
		protected function get_room_data( $pid ) {
			return array(
				'room_regular_price' => get_post_meta( $pid, 'loftocean_room_regular_price', true ),
				'room_min_people' => get_post_meta( $pid, 'loftocean_room_min_people', true ),
				'room_max_people' => get_post_meta( $pid, 'loftocean_room_max_people', true ),
				'room_enable_max_child_number' => get_post_meta( $pid, 'loftocean_room_enable_max_child_number', true ),
				'room_max_child_number' => get_post_meta( $pid, 'loftocean_room_max_child_number', true ),
				'room_enable_max_adult_number' => get_post_meta( $pid, 'loftocean_room_enable_max_adult_number', true ),
				'room_max_adult_number' => get_post_meta( $pid, 'loftocean_room_max_adult_number', true ),
				'room_price_by_people' => get_post_meta( $pid, 'loftocean_room_price_by_people', true ),
				'room_price_per_adult' => get_post_meta( $pid, 'loftocean_room_price_per_adult', true ),
				'room_price_per_child' => get_post_meta( $pid, 'loftocean_room_price_per_child', true ),
				'room_enable_weekend_prices' => get_post_meta( $pid, 'loftocean_room_enable_weekend_prices', true ),
				'room_weekend_price_per_night' => get_post_meta( $pid, 'loftocean_room_weekend_price_per_night', true ),
				'room_weekend_price_per_adult' => get_post_meta( $pid, 'loftocean_room_weekend_price_per_adult', true ),
				'room_weekend_price_per_child' => get_post_meta( $pid, 'loftocean_room_weekend_price_per_child', true ),
				'room_enable_variable_prices' => get_post_meta( $pid, 'loftocean_room_enable_variable_prices', true ),
				'room_enable_variable_guest_group' => get_post_meta( $pid, 'loftocean_room_enable_variable_guest_group', true ),
				'room_enable_variable_weekend_prices' => get_post_meta( $pid, 'loftocean_room_enable_variable_weekend_prices', true ),
				'room_variable_nightly_prices' => get_post_meta( $pid, 'loftocean_room_variable_nightly_prices', true ),
				'room_variable_per_person_prices' => get_post_meta( $pid, 'loftocean_room_variable_per_person_prices', true )
			);
		}
		/**
		* Save room settings
		*/
		public function save_room_settings( $pid ) {
			$regular_price = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_regular_price' ] ) );
			$min_people = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_min_people' ] ) );
			$max_people = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_max_people' ] ) );
			$enable_max_child_number = empty( $_REQUEST[ 'loftocean_room_enable_max_child_number' ] ) ? '' : 'on';
			$max_child_number = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_max_child_number' ] ) );
			$enable_max_adult_number = empty( $_REQUEST[ 'loftocean_room_enable_max_adult_number' ] ) ? '' : 'on';
			$max_adult_number = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_max_adult_number' ] ) );
			$price_by_people = empty( $_REQUEST[ 'loftocean_room_price_by_people' ] ) ? '' : 'on';
			$price_per_adult = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_price_per_adult' ] ) );
			$price_per_child = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_price_per_child' ] ) );

			$enable_weekend_prices = empty( $_REQUEST[ 'loftocean_room_enable_weekend_prices' ] ) ? '' : 'on';
			$weekend_price_per_night = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_weekend_price_per_night' ] ) );
			$weekend_price_per_adult = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_weekend_price_per_adult' ] ) );
			$weekend_price_per_child = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_weekend_price_per_child' ] ) );

			$enable_variable_prices = empty( $_REQUEST[ 'loftocean_room_enable_variable_prices' ] ) ? '' : 'on';
			$enable_variable_guest_group = empty( $_REQUEST[ 'loftocean_room_enable_variable_guest_group' ] ) ? '' : 'on';
			$enable_variable_weekend_prices = empty( $_REQUEST[ 'loftocean_room_enable_variable_weekend_prices' ] ) ? '' : 'on';

			$nightly_variable_prices = isset( $_REQUEST[ 'loftocean_room_variable_nightly_prices' ] ) ? wp_unslash( $_REQUEST[ 'loftocean_room_variable_nightly_prices' ] ) : array();
			$per_person_variable_prices = isset( $_REQUEST[ 'loftocean_room_variable_per_person_prices' ] ) ? wp_unslash( $_REQUEST[ 'loftocean_room_variable_per_person_prices' ] ) : array();

			update_post_meta( $pid, 'loftocean_room_regular_price', $regular_price );
			update_post_meta( $pid, 'loftocean_room_min_people', $min_people );
			update_post_meta( $pid, 'loftocean_room_max_people', $max_people );
			update_post_meta( $pid, 'loftocean_room_enable_max_child_number', $enable_max_child_number );
			update_post_meta( $pid, 'loftocean_room_max_child_number', $max_child_number );
			update_post_meta( $pid, 'loftocean_room_enable_max_adult_number', $enable_max_adult_number );
			update_post_meta( $pid, 'loftocean_room_max_adult_number', $max_adult_number );
			update_post_meta( $pid, 'loftocean_room_price_by_people', $price_by_people );
			update_post_meta( $pid, 'loftocean_room_price_per_adult', $price_per_adult );
			update_post_meta( $pid, 'loftocean_room_price_per_child', $price_per_child );

			update_post_meta( $pid, 'loftocean_room_enable_weekend_prices', $enable_weekend_prices );
			update_post_meta( $pid, 'loftocean_room_weekend_price_per_night', $weekend_price_per_night );
			update_post_meta( $pid, 'loftocean_room_weekend_price_per_adult', $weekend_price_per_adult );
			update_post_meta( $pid, 'loftocean_room_weekend_price_per_child', $weekend_price_per_child );

			update_post_meta( $pid, 'loftocean_room_enable_variable_prices', $enable_variable_prices );
			update_post_meta( $pid, 'loftocean_room_enable_variable_guest_group', $enable_variable_guest_group );
			update_post_meta( $pid, 'loftocean_room_enable_variable_weekend_prices', $enable_variable_weekend_prices );
			update_post_meta( $pid, 'loftocean_room_variable_nightly_prices', array_values( $nightly_variable_prices ) );
			update_post_meta( $pid, 'loftocean_room_variable_per_person_prices', array_values( $per_person_variable_prices ) );
		}
	}
	new Price();
}
