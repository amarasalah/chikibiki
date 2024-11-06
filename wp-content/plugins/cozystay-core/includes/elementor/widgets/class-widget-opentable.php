<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget OpenTable.
 */
class Widget_OpenTable extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanopentable', array( 'id' => 'opentable' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'OpenTable Form', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}
	/**
	 * Get widget categories.
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'loftocean-theme-category' );
	}
	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'opentable', 'open table', 'form' ];
	}
	/**
	* Get JavaScript dependency to render this widget
	* @return array of script handler
	*/
	public function get_script_depends() {
		return array();
	}
	/**
	* Get style dependency to render this widget
	* @return array of style handler
	*/
	public function get_style_depends() {
		return array();
	}
	/**
	 * Register widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section( 'content_section', array(
			'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
		$repeater = new \Elementor\Repeater();
		$repeater->add_control( 'rid',array(
			'label' => esc_html__( 'Restaurant ID (*)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true
		) );
        $repeater->add_control( 'name',array(
			'label' => esc_html__( 'Restaurant Name', 'loftocean' ),
			'description' => esc_html__( 'If there is only one restraurant for booking, you can leave this option empty.', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true
		) );
		$this->add_control( 'list', array(
			'label' => esc_html__( 'Restaurant(s)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => array(
                array(
    				'rid' => '',
                    'name' => ''
                )
            ),
            'title_field' => '{{{ name }}}',
		) );
		$this->add_control( 'language', array(
			'label' => esc_html__( 'Language', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'en-US',
			'options' => array(
				'en-US' => __( 'English-US', 'loftocean' ),
				'fr-CA' => __( 'Français-CA', 'loftocean' ),
				'de-DE' => __( 'Deutsch-DE', 'loftocean' ),
				'es-MX' => __( 'Español-MX', 'loftocean' ),
				'ja-JP' => __( '日本語-JP', 'loftocean' ),
				'nl-NL' => __( 'Nederlands-NL', 'loftocean' ),
				'it-IT' => __( 'Italiano-IT', 'loftocean' )
			)
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_section', array(
			'label' => __( 'Style', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
        $this->add_control( 'style', array(
			'label' => esc_html__( 'Style', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'wide',
			'options' => array(
				'standard'  => esc_html__( 'Standard', 'loftocean' ),
				'wide' => esc_html__( 'Wide', 'loftocean' )
            )
		) );

		$this->start_controls_tabs( 'opentable_form_border' );
        $this->start_controls_tab( 'tab_form_border_normal', array(
        	'label' => esc_html__( 'Normal State', 'loftocean' ),
        ) );

        $this->add_control( 'form_border_color', array(
			'label' => esc_html__( 'Border Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array(
				'{{WRAPPER}}' => '--form-bd: {{VALUE}};',
			)
		) );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_form_border_focus', array(
            'label' => esc_html__( 'Focus State', 'loftocean' )
        ) );

        $this->add_control( 'form_border_focus_color', array(
            'label' => esc_html__( 'Border Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}}' => '--form-bd-focus: {{VALUE}};',
            )
        ) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section( 'button_style_section', array(
			'label' => __( 'Button Color', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->start_controls_tabs( 'tabs_button_style' );
        $this->start_controls_tab( 'tab_button_normal', array(
        	'label' => esc_html__( 'Normal', 'loftocean' ),
        ) );
        $this->add_control( 'button_background_color', array(
			'label' => esc_html__( 'Button Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array(
				'{{WRAPPER}} .button' => '--btn-bg: {{VALUE}};',
			)
		) );
        $this->add_control( 'button_text_color', array(
			'label' => esc_html__( 'Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array(
				'{{WRAPPER}} .button' => '--btn-color: {{VALUE}};',
			)
		) );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_button_hover', array(
            'label' => esc_html__( 'Hover', 'loftocean' ),
        ) );

        $this->add_control( 'button_hover_background_color', array(
            'label' => esc_html__( 'Button Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .button' => '--btn-bg-hover: {{VALUE}};',
            )
        ) );
        $this->add_control( 'button_hover_text_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .button' => '--btn-color-hover: {{VALUE}};',
            )
        ) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

		$this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
		$settings = $this->get_settings_for_display();
		$form_id = $this->get_id() . rand( 1, 999 );
        $rids = array_filter( $settings[ 'list' ], function( $item ) {
			return ! empty( $item[ 'rid' ] );
		} );
        if ( \LoftOcean\is_valid_array( $rids ) ) :
			$has_multiple_rids = count( $rids ) > 1;
			$rid = $has_multiple_rids ? '' : $rids[ 0 ][ 'rid' ];
            $wrapper_class = array( 'cs-open-table', $settings[ 'style' ] );
			$date_format = empty( $settings[ 'date_format' ] ) ? 'yy-mm-dd' : $settings[ 'date_format' ];
			$this->add_render_attribute( 'form_wrapper', array(
				'class' => array( 'cs-otf' ),
				'target' => array( '_blank' ),
				'action' => array( 'https://www.opentable.com/restref/client/' ),
				'data-date-format' => 'YYYY-MM-DD',
				'data-display-date-format' => apply_filters( 'loftocean_display_date_format', 'YYYY-MM-DD' )
			) );
			if ( $has_multiple_rids ) {
				array_push( $wrapper_class, 'multi-restaurants' );
			}
			$this->add_render_attribute( 'wrapper', 'class', $wrapper_class ); ?>

            <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
                <div class="cs-open-table-wrap">
                    <form <?php $this->print_render_attribute_string( 'form_wrapper' ); ?>>
                        <div class="cs-otf-wrap"><?php
							if ( $has_multiple_rids ) : ?>
	                            <div class="cs-otf-field otf-restaurant">
	                                <div class="field-wrap">
	                                	<label class="invisible-label" for="open-table-<?php echo $form_id . '-restaurant'; ?>"><?php esc_html_e( 'Select a Restaurant', 'loftocean' ); ?></label>
	                                    <select class="pick-restaurant" for="open-table-<?php echo $form_id . '-restaurant'; ?>">
											<option value=""><?php esc_html_e( 'Select a restaurant', 'loftocean' ); ?></option><?php
											foreach( $rids as $restaurant ) : ?>
	                                        	<option value="<?php echo esc_attr( $restaurant[ 'rid' ] ); ?>"><?php echo esc_html( $restaurant[ 'name' ] ); ?></option><?php
											endforeach; ?>
	                                    </select>
	                                </div>
	                            </div><?php
							endif; ?>
                            <div class="cs-otf-field otf-size">
                                <div class="field-wrap">
	                                <label class="invisible-label" for="open-table-<?php echo $form_id . '-party-size'; ?>"><?php esc_html_e( 'Select Person Number', 'loftocean' ); ?></label>
                                    <select name="partysize" id="open-table-<?php echo $form_id . '-party-size'; ?>">
                                        <option value="1"><?php esc_html_e( '1 Person', 'loftocean' ); ?></option>
                                        <option value="2"><?php esc_html_e( '2 People', 'loftocean' ); ?></option>
                                        <option value="3"><?php esc_html_e( '3 People', 'loftocean' ); ?></option>
                                        <option value="4"><?php esc_html_e( '4 People', 'loftocean' ); ?></option>
                                        <option value="5"><?php esc_html_e( '5 People', 'loftocean' ); ?></option>
                                        <option value="6"><?php esc_html_e( '6 People', 'loftocean' ); ?></option>
                                        <option value="7"><?php esc_html_e( '7 People', 'loftocean' ); ?></option>
                                        <option value="8"><?php esc_html_e( '8 People', 'loftocean' ); ?></option>
                                        <option value="9"><?php esc_html_e( '9 People', 'loftocean' ); ?></option>
                                        <option value="10"><?php esc_html_e( '10 People', 'loftocean' ); ?></option>
                                        <option value="11"><?php esc_html_e( '11 People', 'loftocean' ); ?></option>
                                        <option value="12"><?php esc_html_e( '12 People', 'loftocean' ); ?></option>
                                        <option value="13"><?php esc_html_e( '13 People', 'loftocean' ); ?></option>
                                        <option value="14"><?php esc_html_e( '14 People', 'loftocean' ); ?></option>
                                        <option value="15"><?php esc_html_e( '15 People', 'loftocean' ); ?></option>
                                        <option value="16"><?php esc_html_e( '16 People', 'loftocean' ); ?></option>
                                        <option value="17"><?php esc_html_e( '17 People', 'loftocean' ); ?></option>
                                        <option value="18"><?php esc_html_e( '18 People', 'loftocean' ); ?></option>
                                        <option value="19"><?php esc_html_e( '19 People', 'loftocean' ); ?></option>
                                        <option value="20"><?php esc_html_e( '20 People', 'loftocean' ); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="cs-otf-field otf-date">
                                <div class="field-wrap">
                                	 <label class="invisible-label" for="open-table-<?php echo $form_id . '-pick-date'; ?>"><?php esc_html_e( 'Pick Date', 'loftocean' ); ?></label>
                                    <input type="text" id="open-table-<?php echo $form_id . '-pick-date'; ?>" value="" class="pick-date" readonly>
                                </div>
                            </div>

                            <div class="cs-otf-field otf-time">
                                <div class="field-wrap">
	                                <label class="invisible-label" for="open-table-<?php echo $form_id . '-time'; ?>"><?php esc_html_e( 'Select Time', 'loftocean' ); ?></label>
                                    <select class="pick-time" id="open-table-<?php echo $form_id . '-time'; ?>">
                                        <option value="00:00" label="12:00 am"><?php esc_html_e( '12:00 am', 'loftocean' ); ?></option>
                                        <option value="00:30" label="12:30 am"><?php esc_html_e( '12:30 am', 'loftocean' ); ?></option>
                                        <option value="01:00" label="1:00 am"><?php esc_html_e( '1:00 am', 'loftocean' ); ?></option>
                                        <option value="01:30" label="1:30 am"><?php esc_html_e( '1:30 am', 'loftocean' ); ?></option>
                                        <option value="02:00" label="2:00 am"><?php esc_html_e( '2:00 am', 'loftocean' ); ?></option>
                                        <option value="02:30" label="2:30 am"><?php esc_html_e( '2:30 am', 'loftocean' ); ?></option>
                                        <option value="03:00" label="3:00 am"><?php esc_html_e( '3:00 am', 'loftocean' ); ?></option>
                                        <option value="03:30" label="3:30 am"><?php esc_html_e( '3:30 am', 'loftocean' ); ?></option>
                                        <option value="04:00" label="4:00 am"><?php esc_html_e( '4:00 am', 'loftocean' ); ?></option>
                                        <option value="04:30" label="4:30 am"><?php esc_html_e( '4:30 am', 'loftocean' ); ?></option>
                                        <option value="05:00" label="5:00 am"><?php esc_html_e( '5:00 am', 'loftocean' ); ?></option>
                                        <option value="05:30" label="5:30 am"><?php esc_html_e( '5:30 am', 'loftocean' ); ?></option>
                                        <option value="06:00" label="6:00 am"><?php esc_html_e( '6:00 am', 'loftocean' ); ?></option>
                                        <option value="06:30" label="6:30 am"><?php esc_html_e( '6:30 am', 'loftocean' ); ?></option>
                                        <option value="07:00" label="7:00 am"><?php esc_html_e( '7:00 am', 'loftocean' ); ?></option>
                                        <option value="07:30" label="7:30 am"><?php esc_html_e( '7:30 am', 'loftocean' ); ?></option>
                                        <option value="08:00" label="8:00 am"><?php esc_html_e( '8:00 am', 'loftocean' ); ?></option>
                                        <option value="08:30" label="8:30 am"><?php esc_html_e( '8:30 am', 'loftocean' ); ?></option>
                                        <option value="09:00" label="9:00 am" selected><?php esc_html_e( '9:00 am', 'loftocean' ); ?></option>
                                        <option value="09:30" label="9:30 am"><?php esc_html_e( '9:30 am', 'loftocean' ); ?></option>
                                        <option value="10:00" label="10:00 am"><?php esc_html_e( '10:00 am', 'loftocean' ); ?></option>
                                        <option value="10:30" label="10:30 am"><?php esc_html_e( '10:30 am', 'loftocean' ); ?></option>
                                        <option value="11:00" label="11:00 am"><?php esc_html_e( '11:00 am', 'loftocean' ); ?></option>
                                        <option value="11:30" label="11:30 am"><?php esc_html_e( '11:30 am', 'loftocean' ); ?></option>
                                        <option value="12:00" label="12:00 pm"><?php esc_html_e( '12:00 pm', 'loftocean' ); ?></option>
                                        <option value="12:30" label="12:30 pm"><?php esc_html_e( '12:30 pm', 'loftocean' ); ?></option>
                                        <option value="13:00" label="1:00 pm"><?php esc_html_e( '1:00 pm', 'loftocean' ); ?></option>
                                        <option value="13:30" label="1:30 pm"><?php esc_html_e( '1:30 pm', 'loftocean' ); ?></option>
                                        <option value="14:00" label="2:00 pm"><?php esc_html_e( '2:00 pm', 'loftocean' ); ?></option>
                                        <option value="14:30" label="2:30 pm"><?php esc_html_e( '2:30 pm', 'loftocean' ); ?></option>
                                        <option value="15:00" label="3:00 pm"><?php esc_html_e( '3:00 pm', 'loftocean' ); ?></option>
                                        <option value="15:30" label="3:30 pm"><?php esc_html_e( '3:30 pm', 'loftocean' ); ?></option>
                                        <option value="16:00" label="4:00 pm"><?php esc_html_e( '4:00 pm', 'loftocean' ); ?></option>
                                        <option value="16:30" label="4:30 pm"><?php esc_html_e( '4:30 pm', 'loftocean' ); ?></option>
                                        <option value="17:00" label="5:00 pm"><?php esc_html_e( '5:00 pm', 'loftocean' ); ?></option>
                                        <option value="17:30" label="5:30 pm"><?php esc_html_e( '5:30 pm', 'loftocean' ); ?></option>
                                        <option value="18:00" label="6:00 pm"><?php esc_html_e( '6:00 pm', 'loftocean' ); ?></option>
                                        <option value="18:30" label="6:30 pm"><?php esc_html_e( '6:30 pm', 'loftocean' ); ?></option>
                                        <option value="19:00" label="7:00 pm"><?php esc_html_e( '7:00 pm', 'loftocean' ); ?></option>
                                        <option value="19:30" label="7:30 pm"><?php esc_html_e( '7:30 pm', 'loftocean' ); ?></option>
                                        <option value="20:00" label="8:00 pm"><?php esc_html_e( '8:00 pm', 'loftocean' ); ?></option>
                                        <option value="20:30" label="8:30 pm"><?php esc_html_e( '8:30 pm', 'loftocean' ); ?></option>
                                        <option value="21:00" label="9:00 pm"><?php esc_html_e( '9:00 pm', 'loftocean' ); ?></option>
                                        <option value="21:30" label="9:30 pm"><?php esc_html_e( '9:30 pm', 'loftocean' ); ?></option>
                                        <option value="22:00" label="10:00 pm"><?php esc_html_e( '10:00 pm', 'loftocean' ); ?></option>
                                        <option value="22:30" label="10:30 pm"><?php esc_html_e( '10:30 pm', 'loftocean' ); ?></option>
                                        <option value="23:00" label="11:00 pm"><?php esc_html_e( '11:00 pm', 'loftocean' ); ?></option>
                                        <option value="23:30" label="11:30 pm"><?php esc_html_e( '11:30 pm', 'loftocean' ); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="cs-otf-field otf-submit">
                                <div class="field-wrap">
                                    <button type="submit" class="button"><span class="btn-text"><?php esc_html_e( 'Book Now', 'loftocean' ); ?></span></button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="rid" value="<?php echo esc_attr( $rid ); ?>">
                        <input type="hidden" name="restref" value="<?php echo esc_attr( $rid ); ?>">
						<input type="hidden" name="lang" value="<?php echo esc_attr( $settings[ 'language' ] ); ?>">
						<input type="hidden" name="domain" value="com">
                        <input type="hidden" name="dateTime" value="">
                    </form>
                </div>
            </div><?php
		elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
			<div class="cs-notice"><?php esc_html_e( 'Please type the Restaurant information first.', 'loftocean' ); ?></div><?php
        endif;
	}
	/**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() { ?>
	    <#
		var rids = [], currentDate = <?php echo json_encode( array(
			'yy-mm-dd'  => date( 'Y-m-d' ),
			'mm-dd-yy'  => date( 'm-d-Y' ),
			'dd-mm-yy'  => date( 'd-m-Y' ),
			'yy/mm/dd'  => date( 'Y/m/d' ),
			'mm/dd/yy'  => date( 'm/d/Y' ),
			'dd/mm/yy'  => date( 'd/m/Y' ),
			'M d, yy'  => date( 'M j, Y' ),
			'd M, yy'  => date( 'j M, Y' )
		) ); ?>;
		_.each( settings[ 'list' ], function( item ) {
			item.rid ? rids.push( item ) : '';
		} );
	    if ( rids.length ) {
	        var rid = rids.length > 1 ? '' : rids[ 0 ][ 'rid' ],
				dateFormat = settings[ 'date_format' ] ? settings[ 'date_format' ] : 'yy-mm-dd';
		    view.addRenderAttribute( 'wrapper', 'class', [ 'cs-open-table', settings[ 'style' ] ] );
			view.addRenderAttribute( 'form_wrapper', 'class', 'cs-otf' );
			view.addRenderAttribute( 'form_wrapper', 'target', '_blank' );
			view.addRenderAttribute( 'form_wrapper', 'action', 'https://www.opentable.com/restref/client/' );
			view.addRenderAttribute( 'form_wrapper', 'data-date-format', dateFormat );
			if ( rids.length > 1 ) {
				view.addRenderAttribute( 'wrapper', 'class', 'multi-restaurants' );
			} #>

	        <div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
	            <div class="cs-open-table-wrap">
	                <form {{{ view.getRenderAttributeString( 'form_wrapper' ) }}}>
	                    <div class="cs-otf-wrap"><#
							if ( rids.length > 1 ) { #>
	                            <div class="cs-otf-field otf-restaurant">
	                                <div class="field-wrap">
	                                    <select class="pick-restaurant">
											<option value=""><?php esc_html_e( 'Select a restaurant', 'loftocean' ); ?></option><#
											_.each( rids, function( restaurant ) { #>
	                                        	<option value="{{ restaurant[ 'rid' ] }}">{{{ restaurant[ 'name' ] }}}</option><#
											} ); #>
	                                    </select>
	                                </div>
	                            </div><#
							} #>
	                        <div class="cs-otf-field otf-size">
	                            <div class="field-wrap">
	                                <select class="" name="partySize">
	                                    <option value="1"><?php esc_html_e( '1 Person', 'loftocean' ); ?></option>
	                                    <option value="2"><?php esc_html_e( '2 People', 'loftocean' ); ?></option>
	                                    <option value="3"><?php esc_html_e( '3 People', 'loftocean' ); ?></option>
	                                    <option value="4"><?php esc_html_e( '4 People', 'loftocean' ); ?></option>
	                                    <option value="5"><?php esc_html_e( '5 People', 'loftocean' ); ?></option>
	                                    <option value="6"><?php esc_html_e( '6 People', 'loftocean' ); ?></option>
	                                    <option value="7"><?php esc_html_e( '7 People', 'loftocean' ); ?></option>
	                                    <option value="8"><?php esc_html_e( '8 People', 'loftocean' ); ?></option>
	                                    <option value="9"><?php esc_html_e( '9 People', 'loftocean' ); ?></option>
	                                    <option value="10"><?php esc_html_e( '10 People', 'loftocean' ); ?></option>
	                                    <option value="11"><?php esc_html_e( '11 People', 'loftocean' ); ?></option>
	                                    <option value="12"><?php esc_html_e( '12 People', 'loftocean' ); ?></option>
	                                    <option value="13"><?php esc_html_e( '13 People', 'loftocean' ); ?></option>
	                                    <option value="14"><?php esc_html_e( '14 People', 'loftocean' ); ?></option>
	                                    <option value="15"><?php esc_html_e( '15 People', 'loftocean' ); ?></option>
	                                    <option value="16"><?php esc_html_e( '16 People', 'loftocean' ); ?></option>
	                                    <option value="17"><?php esc_html_e( '17 People', 'loftocean' ); ?></option>
	                                    <option value="18"><?php esc_html_e( '18 People', 'loftocean' ); ?></option>
	                                    <option value="19"><?php esc_html_e( '19 People', 'loftocean' ); ?></option>
	                                    <option value="20"><?php esc_html_e( '20 People', 'loftocean' ); ?></option>
	                                </select>
	                            </div>
	                        </div>

	                        <div class="cs-otf-field otf-date">
	                            <div class="field-wrap">
	                                <input type="text" value="{{{ currentDate[ dateFormat ] }}}" class="pick-date">
	                            </div>
	                        </div>

	                        <div class="cs-otf-field otf-time">
	                            <div class="field-wrap">
	                                <select class="pick-time">
	                                    <option value="00:00" label="12:00 am"><?php esc_html_e( '12:00 am', 'loftocean' ); ?></option>
	                                    <option value="00:30" label="12:30 am"><?php esc_html_e( '12:30 am', 'loftocean' ); ?></option>
	                                    <option value="01:00" label="1:00 am"><?php esc_html_e( '1:00 am', 'loftocean' ); ?></option>
	                                    <option value="01:30" label="1:30 am"><?php esc_html_e( '1:30 am', 'loftocean' ); ?></option>
	                                    <option value="02:00" label="2:00 am"><?php esc_html_e( '2:00 am', 'loftocean' ); ?></option>
	                                    <option value="02:30" label="2:30 am"><?php esc_html_e( '2:30 am', 'loftocean' ); ?></option>
	                                    <option value="03:00" label="3:00 am"><?php esc_html_e( '3:00 am', 'loftocean' ); ?></option>
	                                    <option value="03:30" label="3:30 am"><?php esc_html_e( '3:30 am', 'loftocean' ); ?></option>
	                                    <option value="04:00" label="4:00 am"><?php esc_html_e( '4:00 am', 'loftocean' ); ?></option>
	                                    <option value="04:30" label="4:30 am"><?php esc_html_e( '4:30 am', 'loftocean' ); ?></option>
	                                    <option value="05:00" label="5:00 am"><?php esc_html_e( '5:00 am', 'loftocean' ); ?></option>
	                                    <option value="05:30" label="5:30 am"><?php esc_html_e( '5:30 am', 'loftocean' ); ?></option>
	                                    <option value="06:00" label="6:00 am"><?php esc_html_e( '6:00 am', 'loftocean' ); ?></option>
	                                    <option value="06:30" label="6:30 am"><?php esc_html_e( '6:30 am', 'loftocean' ); ?></option>
	                                    <option value="07:00" label="7:00 am"><?php esc_html_e( '7:00 am', 'loftocean' ); ?></option>
	                                    <option value="07:30" label="7:30 am"><?php esc_html_e( '7:30 am', 'loftocean' ); ?></option>
	                                    <option value="08:00" label="8:00 am"><?php esc_html_e( '8:00 am', 'loftocean' ); ?></option>
	                                    <option value="08:30" label="8:30 am"><?php esc_html_e( '8:30 am', 'loftocean' ); ?></option>
	                                    <option value="09:00" label="9:00 am" selected><?php esc_html_e( '9:00 am', 'loftocean' ); ?></option>
	                                    <option value="09:30" label="9:30 am"><?php esc_html_e( '9:30 am', 'loftocean' ); ?></option>
	                                    <option value="10:00" label="10:00 am"><?php esc_html_e( '10:00 am', 'loftocean' ); ?></option>
	                                    <option value="10:30" label="10:30 am"><?php esc_html_e( '10:30 am', 'loftocean' ); ?></option>
	                                    <option value="11:00" label="11:00 am"><?php esc_html_e( '11:00 am', 'loftocean' ); ?></option>
	                                    <option value="11:30" label="11:30 am"><?php esc_html_e( '11:30 am', 'loftocean' ); ?></option>
	                                    <option value="12:00" label="12:00 pm"><?php esc_html_e( '12:00 pm', 'loftocean' ); ?></option>
	                                    <option value="12:30" label="12:30 pm"><?php esc_html_e( '12:30 pm', 'loftocean' ); ?></option>
	                                    <option value="13:00" label="1:00 pm"><?php esc_html_e( '1:00 pm', 'loftocean' ); ?></option>
	                                    <option value="13:30" label="1:30 pm"><?php esc_html_e( '1:30 pm', 'loftocean' ); ?></option>
	                                    <option value="14:00" label="2:00 pm"><?php esc_html_e( '2:00 pm', 'loftocean' ); ?></option>
	                                    <option value="14:30" label="2:30 pm"><?php esc_html_e( '2:30 pm', 'loftocean' ); ?></option>
	                                    <option value="15:00" label="3:00 pm"><?php esc_html_e( '3:00 pm', 'loftocean' ); ?></option>
	                                    <option value="15:30" label="3:30 pm"><?php esc_html_e( '3:30 pm', 'loftocean' ); ?></option>
	                                    <option value="16:00" label="4:00 pm"><?php esc_html_e( '4:00 pm', 'loftocean' ); ?></option>
	                                    <option value="16:30" label="4:30 pm"><?php esc_html_e( '4:30 pm', 'loftocean' ); ?></option>
	                                    <option value="17:00" label="5:00 pm"><?php esc_html_e( '5:00 pm', 'loftocean' ); ?></option>
	                                    <option value="17:30" label="5:30 pm"><?php esc_html_e( '5:30 pm', 'loftocean' ); ?></option>
	                                    <option value="18:00" label="6:00 pm"><?php esc_html_e( '6:00 pm', 'loftocean' ); ?></option>
	                                    <option value="18:30" label="6:30 pm"><?php esc_html_e( '6:30 pm', 'loftocean' ); ?></option>
	                                    <option value="19:00" label="7:00 pm"><?php esc_html_e( '7:00 pm', 'loftocean' ); ?></option>
	                                    <option value="19:30" label="7:30 pm"><?php esc_html_e( '7:30 pm', 'loftocean' ); ?></option>
	                                    <option value="20:00" label="8:00 pm"><?php esc_html_e( '8:00 pm', 'loftocean' ); ?></option>
	                                    <option value="20:30" label="8:30 pm"><?php esc_html_e( '8:30 pm', 'loftocean' ); ?></option>
	                                    <option value="21:00" label="9:00 pm"><?php esc_html_e( '9:00 pm', 'loftocean' ); ?></option>
	                                    <option value="21:30" label="9:30 pm"><?php esc_html_e( '9:30 pm', 'loftocean' ); ?></option>
	                                    <option value="22:00" label="10:00 pm"><?php esc_html_e( '10:00 pm', 'loftocean' ); ?></option>
	                                    <option value="22:30" label="10:30 pm"><?php esc_html_e( '10:30 pm', 'loftocean' ); ?></option>
	                                    <option value="23:00" label="11:00 pm"><?php esc_html_e( '11:00 pm', 'loftocean' ); ?></option>
	                                    <option value="23:30" label="11:30 pm"><?php esc_html_e( '11:30 pm', 'loftocean' ); ?></option>
	                                </select>
	                            </div>
	                        </div>

	                        <div class="cs-otf-field otf-submit">
	                            <div class="field-wrap">
	                                <button type="submit" class="button"><span class="btn-text"><?php esc_html_e( 'Book Now', 'loftocean' ); ?></span></button>
	                            </div>
	                        </div>
	                    </div>
	                    <input type="hidden" name="rid" value="{{ rid }}">
	                    <input type="hidden" name="restref" value="{{ rid }}">
	                    <input type="hidden" name="dateTime" value="">
	                </form>
	            </div>
	        </div><#
	    } else { #>
	        <div class="cs-notice"><?php esc_html_e( 'Please type the Restaurant information first.', 'loftocean' ); ?></div><#
	    } #><?php
	}
}
