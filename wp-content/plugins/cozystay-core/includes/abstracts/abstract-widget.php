<?php
namespace LoftOcean;
/**
* Abstract widget class
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * LoftOcean_Widget
 *
 * @since  	1.0.0
 * @extends WP_Widget
 */
abstract class Widget extends \WP_Widget {
	/**
	* Array widget settings list
	*/
	public $settings = array();
	/**
	* Array widget settings dependency
	*/
	public $dependency = array();
	/**
	* Array widget settings json
	*/
	public $json = array();
	/**
	* Array default widget setting values
	*/
	public $defaults = array();
	/**
	* Array current widget setting values
	*/
	public $instance = array();
	/**
	* Boolean
	*/
	protected $force_add_widget = false;
	/**
	* Template wrap start for form element render
	*/
	protected $templates_start = array(
		'default' => '<p class="item-wrapper item-type-%1$s" id="%2$s-%3$s">',
		'image' => '<div class="media-widget-control item-wrapper item-type-%1$s" id="%2$s-%3$s">'
	);
	/**
	* Template wrap end for form element render
	*/
	protected $templates_end = array(
		'default' => '</p>',
		'image' => '</div>'
	);
	/**
	* Construct function
	*/
	public function __construct( $widget_id, $widget_name, $widget_options = array() ) {
		parent::__construct( $widget_id, $widget_name, $widget_options );

		add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_custom_script' ) );
		add_action( 'admin_footer-widgets.php', array( $this, 'print_custom_template' ) );

		add_filter( 'loftocean_get_widget_json', array( $this, 'get_json' ) );
	}
	/**
	* The buildin function to output the setting html for frontend
	 * @param array $args Arguments.
	 * @param array $instance Instance.
	*/
	public function widget( $args, $instance ) {
		$this->register_settings();
		$this->instance = $instance;
		$this->widget_start( $args, $instance );
		$this->widget_content();
		$this->widget_end( $args );
	}
	/**
	 * Output the html at the start of a widget.
	 *
	 * @param array $args Arguments.
	 * @param array $instance Instance.
	 */
	public function widget_start( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.Code lysis.AssignmentInCondition.Found
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		if ( !  empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}
	}
	/**
	 * Output the html at the end of a widget.
	 *
	 * @param  array $args Arguments.
	 */
	public function widget_end( $args ) {
		echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}
	/**
	 * Output the main content for each widget
	 * @param array $args Arguments.
	 * @param array $instance Instance.
	*/
	public function widget_content() { }
	/**
	* The buildin function to output the setting form
	* @param array current setting values
	*/
	public function form( $instance ) {
		$this->register_settings();
		$this->instance = $instance;
		$this->render();
	}
	/**
	* Render form for widget settings
	* 	Will wrap each element with <p>
	*/
	protected function render() {
		if ( $this->settings && is_array( $this->settings ) ) {
			$widget_id = $this->id;
			$template_keys = array_keys( $this->templates_start );
			foreach ( $this->settings as $id => $setting ) {
				$type = in_array( $setting['type'], $template_keys ) ? $setting['type'] : 'default';
				printf(
					wp_kses_post( $this->templates_start[ $type ] ),
					esc_attr( $setting['type'] ),
					esc_attr( $widget_id ),
					esc_attr( $setting['id'] )
				);
				$this->the_setting_html( $setting );
				echo wp_kses_post( $this->templates_end[ $type ] );
			}
		}
	}
	/**
	* Output the field title
	* @param array
	*/
	public function the_field_title( $setting ) {
		if ( ! empty( $setting['title'] ) ) : ?>
			<label class="title"><?php echo esc_html( $setting['title'] ); ?></label><?php
		endif;
	}
	/**
	* Output the html for each type of setting
	* @param array setting
	*/
	public function the_setting_html( $setting ) {
		$setting_id = $setting['id'];
		$value 		= $this->get_value( $setting_id );
		$field_id 	= $this->get_field_id( $setting_id );
		$field_name = $this->get_field_name( $setting_id );
		switch ( $setting['type'] ) {
			case 'radio':
				if ( ! empty( $setting['choices'] ) ) :
					$this->the_field_title( $setting );
					foreach ( $setting['choices'] as $val => $label ) :
						$fid = sprintf( '%s-%s', $field_id, $val ); ?>
						<label class="radio-wrapper" for="%1$s">
							<input
								type="radio"
								id="<?php echo esc_attr( $fid ); ?>"
								name="<?php echo esc_attr( $field_name ); ?>"
								value="<?php echo esc_attr( $val ); ?>"
								<?php checked( $val, $value ); ?>
								<?php $this->the_attrs( $setting['input_attr'] ); ?>
							/>
							<?php echo esc_html( $label ); ?>
						</label> <?php
					endforeach;
				endif;
				break;
			case 'radio-with-thumbnail':
				if ( ! empty( $setting['choices'] ) ) :
					$this->the_field_title( $setting ); ?>
					<div class="<?php echo esc_attr( $this->id_base ); ?>-<?php echo esc_attr( $setting_id ); ?>-radio-btn-wrap"> <?php
					foreach ( $setting['choices'] as $val => $attr ) :
						$fid = sprintf( '%s-%s', $field_id, $val ); ?>
						<label class="radio-wrapper" for="<?php echo esc_attr( $fid ); ?>">
							<input
								type="radio"
								id="<?php echo esc_attr( $fid ); ?>"
								name="<?php echo esc_attr( $field_name ); ?>"
								value="<?php echo esc_attr( $val ); ?>"
								<?php checked( $val, $value ); ?>
								<?php $this->the_attrs( $setting['input_attr'] ); ?>
							/>
							<span class="thumbnail"></span>
							<span class="thumbnail-title"><?php echo esc_html( $attr ); ?></span>
						</label><?php
					endforeach; ?>
					</div><?php
				endif;
				break;
			case 'select':
				if ( ! empty( $setting['choices'] ) ) :
					$value = is_array( $value ) ? $value : array( $value );
					if ( ! empty( $setting['title'] ) ) : ?>
						<label class="title" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $setting['title'] ); ?></label> <?php
					endif;
					if ( ! empty( $setting['description' ] ) && empty ( $setting['description_below'] ) ) : ?>
						<span class="homepage-widget-control-description description"><?php echo wp_kses_post( $setting['description'] ); ?></span> <?php
					endif; ?>
					<select
						id="<?php echo esc_attr( $field_id ); ?>"
						name="<?php echo esc_attr( $field_name ); ?><?php if ( in_array( 'multiple', $setting['input_attr'] ) ) : ?>[]" style="height: 100%;<?php endif; ?>"
						<?php $this->the_attrs( $setting['input_attr'] ); ?>
					>
					<?php foreach ( $setting['choices'] as $val => $attr ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>"<?php if ( in_array( $val, $value ) ) : ?> selected="selected"<?php endif; ?>><?php echo esc_html( $attr ); ?></option>
					<?php endforeach; ?>
					</select>
					<?php if ( ! empty( $setting['description' ] ) && ! empty( $setting['description_below'] ) ) : ?>
						<span class="homepage-widget-control-description description"><?php echo wp_kses_post( $setting['description'] ); ?></span>
					<?php endif;
				endif;
				break;
			case 'select-sortable':
				if ( ! empty( $setting['choices'] ) ) :
					$value = is_array( $value ) ? implode( ',', $value ) : $value;
					if ( ! empty( $setting['title'] ) ) : ?>
						<label class="title" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $setting['title'] ); ?></label> <?php
					endif;
					if ( ! empty( $setting['description' ] ) && empty ( $setting['description_below'] ) ) : ?>
						<span class="homepage-widget-control-description description"><?php echo wp_kses_post( $setting['description'] ); ?></span> <?php
					endif; ?>
					<select multiple class="sortable-selection-list">
					<?php foreach ( $setting['choices'] as $val => $attr ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $attr ); ?></option>
					<?php endforeach; ?>
					</select>
					<?php if ( ! empty( $setting['description' ] ) && ! empty( $setting['description_below'] ) ) : ?>
						<span class="homepage-widget-control-description description"><?php echo wp_kses_post( $setting['description'] ); ?></span>
					<?php endif; ?>
					<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="sortable-selection-value" <?php $this->the_attrs( $setting['input_attr'] ); ?> />
					<ul class="sortable"></ul><?php
				endif;
				break;
			case 'color-picker':
				$default_color = $this->defaults[ $setting_id ];
				if ( empty( $default_color ) ) {
					$default_color = '#RRGGBB';
				}
				if ( ! empty( $setting['title'] ) ) : ?>
					<label class="title" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $setting['title'] ); ?></label> <?php
				endif; ?>
				<input
					id="<?php echo esc_attr( $field_id ); ?>"
					name="<?php echo esc_attr( $field_name ); ?>"
					type="text"
					value="<?php echo esc_attr( $value ); ?>"
					placeholder="<?php echo esc_attr( $default_color ); ?>"
					<?php $this->the_attrs( $setting['input_attr'] ); ?>
				/> <?php
				break;
			case 'checkbox': ?>
				<input
					type="checkbox"
					id="<?php echo esc_attr( $field_id ); ?>"
					name="<?php echo esc_attr( $field_name ); ?>"
					value="on"
					<?php checked( $value, 'on' ); ?>
					<?php $this->the_attrs( $setting['input_attr'] ); ?>
				/>
				<?php if ( ! empty( $setting['title'] ) ) : ?>
					<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $setting['title'] ); ?></label>
				<?php endif; ?>
				<?php if ( ! empty( $setting['description'] ) ) : ?>
					<span class="homepage-widget-control-description description" style="display: block;"><?php echo wp_kses_post( $setting['description'] ); ?></span>
				<?php endif;
				break;
			case 'editor':
				$this->the_field_title( $setting ); ?>
				<div class="hide editor-textarea-wrap">
					<textarea
						data-id="<?php echo esc_attr( $field_id ); ?>"
						name="<?php echo esc_attr( $field_name ); ?>"
						<?php $this->the_attrs( $setting['input_attr'] ); ?>
					><?php echo esc_textarea( $value ); ?></textarea>
				</div><?php
				break;
			case 'image':
				$media_id 	= false;
				$has_media 	= false;
				$image_only = empty( $setting['media_types'] ) || ( 'image' === $setting['media_types'] );
				$i18n_text 	= array(
					'preview'	=> $image_only ? esc_html__( 'No image selected', 'loftocean' ) : esc_html__( 'No media selected', 'loftocean' ),
					'choose' 	=> $image_only ? esc_html__( 'Choose Image', 'loftocean' ) : esc_html__( 'Choose Media', 'loftocean' ),
					'remove' 	=> $image_only ? esc_html__( 'Remove Image', 'loftocean' ) : esc_html__( 'Remove Media', 'loftocean' )
				);

				if ( $image_only ) {
					$media_id = isset( $value ) ? absint( $value ) : '';
				} else {
					$value = empty( $value ) ? array( 'type' => '', 'id' => '' ) : $value;
					$media_id = absint( $value['id'] );
				}
				$has_media = \LoftOcean\media_exists( $media_id );

				$this->the_field_title( $setting ); ?>
				<div class="media-widget-preview<?php if ( ! $image_only ) : ?> media<?php endif; ?>" data-text-preview="<?php echo esc_attr( $i18n_text['preview'] ); ?>"> <?php
					if ( $has_media ) :
						if ( $image_only ) :
							$image_info = wp_get_attachment_image_src( $media_id, 'medium' );
							$image_alt = \LoftOcean\get_image_alt( $media_id ); ?>
							<img width=<?php echo esc_attr( $image_info[1] ); ?> height=<?php echo esc_attr( $image_info[2] ); ?> class="attachment-thumb" src="<?php echo esc_url( $image_info[0] ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>"> <?php
						else :
							$media_src = wp_get_attachment_url( $media_id );
							if ( 'image' === $value['type'] ) :
								$image_info = wp_get_attachment_image_src( $media_id, 'medium' );
								$media_alt	= \LoftOcean\get_image_alt( $media_id ); ?>
								<img width=<?php echo esc_attr( $image_info[1] ); ?> height=<?php echo esc_attr( $image_info[2] ); ?> class="attachment-thumb" src="<?php echo esc_url( $media_src ); ?>" alt="<?php echo esc_attr( $media_alt ); ?>"> <?php
							else : ?>
								<video controls class="attachment-thumb" src="<?php echo esc_url( $media_src ); ?>"></video> <?php
							endif;
						endif;
					else : ?>
						<div class="placeholder"><?php echo esc_html( $i18n_text['preview'] ); ?></div> <?php
					endif; ?>
				</div>
				<p class="media-widget-buttons">
					<button type="button" class="button widget-choose-media<?php if ( ! $image_only ) : ?> media<?php endif; ?> not-selected">
						<?php echo esc_html( $i18n_text['choose'] ); ?>
					</button>
					<button type="button" class="button widget-remove-media <?php if ( $has_media ) : ?>not-<?php endif; ?>selected">
						<?php echo esc_html( $i18n_text['remove'] ); ?>
					</button>
				</p> <?php
				if ( $image_only ) : ?>
					<input name="<?php echo esc_attr( $field_name ); ?>" type="hidden" value="<?php echo esc_attr( $value ); ?>"<?php $this->the_attrs( $setting['input_attr'] ); ?> /> <?php
				else :
					$value = empty( $value ) ? array( 'type' => '', 'id' => '' ) : $value; ?>
					<input name="<?php echo esc_attr( $field_name ); ?>[id]" type="hidden" value="<?php echo esc_attr( $value['id'] ); ?>"<?php $this->the_attrs( $setting['input_attr'] ); ?> />
					<input name="<?php echo esc_attr( $field_name ); ?>[type]" type="hidden" value="<?php echo esc_attr( $value['type'] ); ?>" /> <?php
				endif;
				break;
			case 'title':
				$this->the_field_title( $setting );
				break;
			case 'description':
				$this->the_field_title( $setting );
				if ( ! empty( $setting['description'] ) ) : ?>
					<span class="widget-control-description"><?php echo wp_kses_post( $setting['description'] ); ?></span> <?php
				endif;
				break;
			case 'textarea':
				$this->the_field_title( $setting ); ?>
				<textarea
					id="<?php echo esc_attr( $field_id ); ?>"
					name="<?php echo esc_attr( $field_name ); ?>"
					<?php $this->the_attrs( $setting['input_attr'] ); ?>
				><?php echo esc_textarea( $value ); ?></textarea><?php
				break;
			case 'html':
				if ( ! empty( $setting['html'] ) ) {
					echo wp_kses_post( $setting['html'] );
				}
				break;
			case 'slider':
				$after_text = isset( $setting['after_text'] ) ? $setting['after_text'] : ''; ?>
				<label class="title">
					<?php if ( ! empty( $setting['title'] ) ) : ?>
						<span class="slider-control-title"><?php echo esc_html( $setting['title'] ); ?></span>
					<?php endif; ?>
					<span class="amount opacity" style="float: right; ">
						<input
							readonly="readonly"
							id="<?php echo esc_attr( $field_id ); ?>"
							name="<?php echo esc_attr( $field_name ); ?>"
							type="text"
							value="<?php echo esc_attr( $value ); ?>"
							<?php $this->the_attrs( $setting['input_attr'] ); ?>
						/>
						<?php echo esc_html( $after_text ); ?>
					</span>
				</label>
				<div class="ui-slider loader-ui-slider" data-value="<?php echo esc_attr( $value ); ?>"<?php $this->the_attrs( $setting['slider_attr'] ); ?>></div> <?php
				break;
			default:
				if ( ! empty( $setting['title'] ) ) : ?>
					<label
						class="title"
						for="<?php echo esc_attr( $field_id ); ?>"
						<?php if ( ! empty( $setting['label_style'] ) ) : ?> style="<?php echo esc_attr( $setting['label_style'] ); ?>"<?php endif; ?>
					><?php echo esc_html( $setting['title'] ); ?></label> <?php
				endif; ?>
				<input
					id="<?php echo esc_attr( $field_id ); ?>"
					name="<?php echo esc_attr( $field_name ); ?>"
					type="<?php echo esc_attr( $setting['type'] ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					<?php $this->the_attrs( $setting['input_attr'] ); ?>
				/> <?php
				if ( ! empty( $setting['text_after'] ) ) {
					echo esc_html( $setting['text_after'] );
				}
				if ( ! empty( $setting['description'] ) ) : ?>
					<span class="widget-control-description description"><?php echo wp_kses_post( $setting['description'] ); ?></span> <?php
				endif;
		}
	}
	/**
	* Output the html attributes string by given attributes
	* @param array attributes list
	*/
	private function the_attrs( $attrs ) {
		if ( ! empty( $attrs ) && is_array( $attrs ) ) {
			foreach( $attrs as $name => $value ) {
				printf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
			}
		}
	}
	/**
	 * Handles updating the setting values.
	 */
	public function update( $new_instance, $old_instance ) {
		$old_instance = array_merge( $this->defaults, $old_instance );
		$instance = $old_instance;
		$this->register_settings();

		if ( empty( $this->settings ) || ! is_array( $this->settings ) ) {
			return $instance;
		}

		foreach ( $this->settings as $id => $setting ) {
			if ( ! isset( $setting, $setting['sanitize'] ) ) continue;
			$new_value = isset( $new_instance, $new_instance[ $id ] ) ? $new_instance[ $id ] : '';
			$old_value = isset( $old_instance, $old_instance[ $id ] ) ? $old_instance[ $id ] : '';
			switch ( $setting['sanitize'] ) {
				case 'checkbox':
					$new_value = empty( $new_value ) ? '' : 'on';
					break;
				case 'choice':
					$choices = empty( $setting['choices'] ) ? false : array_keys( $setting['choices'] );
					$value = ( $choices && in_array( $new_value, $choices ) ) ? $new_value : $old_value;
					break;
				case 'choices':
					if ( empty( $new_value ) ) {
						$new_value = array();
					} else {
						$passed	= true;
						$choices = empty( $setting['choices'] ) ? false : array_keys( $setting['choices'] );
						if ( $choices ) {
							foreach ( $new_value as $val ) {
								if ( ! in_array( $val, $choices ) ) {
									$pass = false;
									break;
								}
							}
						}
						$new_value = $passed ? $new_value : $old_value;
					}
					break;
				case 'number':
					$new_value = absint( $new_value );
					// Check minimum setting
					if ( isset( $setting['min'] ) && ( '' !== $setting['min'] ) ) {
						$new_value = max( $new_value, $setting['min'] );
					}
					// Check maximum setting
					if ( isset( $setting['max'] ) && ( '' !== $setting['max'] ) ) {
						$new_value = min( $new_value, $setting['max'] );
					}
					break;
				case 'html':
					$new_value = empty( $new_value ) ? '' : wp_kses( trim( wp_unslash( $new_value ) ), get_custom_content_allowed_html() );
					break;
				case 'color':
					$color = sanitize_hex_color( $new_value );
					$new_value = $color ? $color : '';
					break;
				case 'url':
					$new_value = empty( $new_value ) ? '' : esc_url_raw( $new_value );
					break;
				case 'media':
					if ( in_array( $new_value['type'], array( 'image', 'video' ) ) || ( isset( $new_value['type'] ) && empty( $new_value['id'] ) ) ) {
						$new_value = array( 'id' => intval( $new_value['id'] ), 'type' => $new_value['type'] );
					} else {
						$new_value = $old_value;
					}
					break;
				case 'empty':
					$new_value = '';
					break;
				default:
					$new_value = empty( $new_value ) ? '' : sanitize_text_field( $new_value );
					break;
			}
			$instance[ $id ] = apply_filters( 'loftocean_widget_settings_sanitize_option', $new_value, $new_instance, $id, $setting );
		}

		$this->flush_widget_cache();

		return $instance;
	}
	/**
	* Add the default input attributes to widget control
	* @param array widget control with id, type, default value....
	* @return array widget control
	*/
	public function add_attributes( $setting ) {
		$data_item_id = array( 'data-loftocean-widget-item-id' => $setting['id'] );
		$data_item_class = sprintf(
			'loftocean-widget-item%s%s',
			( 'color-picker' == $setting['type'] ? ' loftocean-color-picker' : ( $setting['widefat'] ? ' widefat' : '' ) ),
			empty( $setting['input_attr']['class'] ) ? '' : sprintf( ' %s', $setting['input_attr']['class'] )
		);

		$setting['input_attr'] = empty( $setting['input_attr'] ) ? $data_item_id : array_merge( $setting['input_attr'], $data_item_id );
		$setting['input_attr']['class'] = $data_item_class;
		return $setting;
	}
	/**
	* Dynamically add the widget setting
	* @param array setting need to be added
	*/
	public function add_setting( $setting ) {
		$setting = array_merge( array( 'id' => '', 'type' => '', 'default' => 'not set', 'widefat' => true ), $setting );
		if ( ! empty( $setting['id'] ) && ! empty( $setting['type'] ) && ( 'not set' != $setting['default'] ) ) {
			$setting = $this->add_attributes( $setting );
			$sid = $setting['id'];
			$this->settings[ $sid ] = $setting;
			$this->defaults[ $sid ] = $setting['default'];
			if ( ! empty( $setting['dependency'] ) ) {
				$this->generate_dependency( $sid, $setting['dependency'] );
				$this->json[ $sid ] = $setting['dependency'];
			}
			$this->check_more_settings( $sid );
		}
	}
	/**
	* Helper function to generate dependency
	*/
	protected function generate_dependency( $sid, $deps ) {
		$is_complex = ! empty( $deps['is_complex'] );
		unset( $deps['is_complex'], $deps['relation'] );
		if ( $is_complex ) {
			foreach ( $deps as $dep ) {
				if ( is_array( $dep ) ) {
					$this->generate_dependency( $sid, $dep );
				}
			}
		} else{
			foreach ( $deps as $id => $val ) {
				if ( is_array( $val ) ) {
					if ( empty( $this->dependency[ $id ] ) ) {
						$this->dependency[ $id ] = array();
					}
					$this->dependency[ $id ][] = $sid;
				}
			}
		}
	}
	/**
	* Check more settings after current setting
	* @param string current setting id
	*/
	protected function check_more_settings( $current_id ) {
		$more_settings = apply_filters( 'loftocean_get_widget_more_settings', false, array( 'widget' => $this->id_base, 'setting' => $current_id ) );
		if ( $more_settings ) {
			foreach( $more_settings as $ms ) {
				$this->add_setting( $ms );
			}
		}
	}
	/***
	* The actual function to output the json and dependency js vars
	* @param array values before changed
	* @return array values after changed
	*/
	public function get_json( $json = array() ) {
		$widget_id = $this->id_base;
		if ( isset( $_GET[ 'action' ] ) && ( 'elementor' == $_GET[ 'action' ] ) ) {
			$this->register_settings();
		}
		if ( ! empty( $this->json ) && ! empty( $this->dependency ) ) {
			if ( empty( $json['widgets'] ) ) {
				$json['widgets'] = array();
			}
			if ( empty( $json['JSON'] ) ) {
				$json['JSON'] = array();
			}
			if ( empty( $json['dependency'] ) ) {
				$json['dependency'] = array();
			}

			array_push( $json['widgets'], $widget_id );
			$json['JSON'][ $widget_id ] = $this->json;
			$json['dependency'][ $widget_id ] = $this->dependency;
		} else if ( $this->force_add_widget ) {
			if ( empty( $json['widgets'] ) ) {
				$json['widgets'] = array();
			}
			array_push( $json['widgets'], $widget_id );
		}
		return $json;
	}
	/**
	* Register the widget settings
	* 	Must be overwrite in child class
	*/
	public function register_settings() { }
	/**
	* Print widget custom template if needed
	*/
	public function print_custom_template() { }
	/**
	* Enqueue custom script if needed
	*/
	public function enqueue_custom_script() { }
	/**
	 * Get cached widget.
	 *
	 * @param  array $args Arguments.
	 * @return bool true if the widget is cached otherwise false
	 */
	public function get_cached_widget( $args ) {
		$cache = wp_cache_get( $this->get_widget_id_for_cache( $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		$widget_cache_id = $this->get_widget_id_for_cache( $args['widget_id'] );
		if ( isset( $cache[ $widget_cache_id ] ) ) {
			echo $cache[ $widget_cache_id ]; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
			return true;
		}

		return false;
	}
	/**
	 * Cache the widget.
	 *
	 * @param  array  $args Arguments.
	 * @param  string $content Content.
	 * @return string the content that was cached
	 */
	public function cache_widget( $args, $content ) {
		$cache = wp_cache_get( $this->get_widget_id_for_cache( $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		$cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ] = $content;

		wp_cache_set( $this->get_widget_id_for_cache( $this->widget_id ), $cache, 'widget' );

		return $content;
	}

	/**
	 * Flush the cache.
	 */
	public function flush_widget_cache() {
		if ( ! empty( $this->widget_id ) ) {
			foreach ( array( 'https', 'http' ) as $scheme ) {
				wp_cache_delete( $this->get_widget_id_for_cache( $this->widget_id, $scheme ), 'widget' );
			}
		}
	}
	/**
	 * Get widget id plus scheme/protocol to prevent serving mixed content from (persistently) cached widgets.
	 *
	 * @since  3.4.0
	 * @param  string $widget_id Id of the cached widget.
	 * @param  string $scheme    Scheme for the widget id.
	 * @return string            Widget id including scheme/protocol.
	 */
	protected function get_widget_id_for_cache( $widget_id, $scheme = '' ) {
		if ( $scheme ) {
			$widget_id_for_cache = $widget_id . '-' . $scheme;
		} else {
			$widget_id_for_cache = $widget_id . '-' . ( is_ssl() ? 'https' : 'http' );
		}

		return apply_filters( 'loftocean_cached_widget_id', $widget_id_for_cache );
	}
	/**
	* Help function to get setting value by its id, return the default value if not set
	* @param string widget setting id
	* @return mix setting's current value
	*/
	public function get_value( $id ) {
		return isset( $this->instance[ $id ] ) ? $this->instance[ $id ] : $this->defaults[ $id ];
	}
	/**
	* Help functin to test checkbox type setting is checked
	* @param setting id
	* @return boolean true if check, otherwise false
	*/
	protected function is_checked( $id ) {
		return ( 'on' == $this->get_value( $id ) );
	}
}
