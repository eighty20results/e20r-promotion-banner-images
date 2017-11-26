<?php
/*
Plugin Name: E20R Promotion Banner Image Widget
Plugin URI: http://eighty20results.com/paid-memberships-pro/do-it-for-me
Description: Add widget area promotion images with timed visibility (when to start being visible and when to stop being visible). Optionally specify a title, the image, a description, etc.
Version: 2.1
Author: Thomas Sjolshagen <thomas@eighty20results.com>
Author URI: https://eighty20results.com/thomas-sjolshagen/
Text Domain: e20r-promotion-banner-images
Domain Path: /languages
Tags: sidebar widget, banner widget, timed sidebar image
*/

namespace E20R\Promotion_Banners;
/**
 * Plugin Basename
 */
if ( ! defined( 'E20R_PLUGIN_BASENAME' ) ) {
	define( 'E20R_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
/**
 * Version is defined
 */
define( 'E20R_PBI_VERSION', '2.1' );

if ( ! defined( 'E20R_PLUGIN_NAME' ) ) {
	define( 'E20R_PLUGIN_NAME', trim( dirname( E20R_PLUGIN_BASENAME ), '/' ) );
}

/**
 * E20R Promotion Banner Images Class
 */
if ( ! class_exists( 'E20R\Promotion_Banners\EBPI' ) ) {
	/**
	 * Class E20R\Promotion_Banners\EBPI
	 */
	class EBPI extends \WP_Widget {
		/**
		 * @var null|EBPI
		 */
		private static $instance = null;
		
		/**
		 * EBPI constructor.
		 */
		public function __construct() {
			
			$widget_ops = array(
				'classname'   => 'EBPI',
				'description' => __( 'Adds image banner to a widget area with timed visibility (dates)' ),
			);
			
			parent::__construct( 'EPBI', __( 'E20R Promotion Banner Image Widget' ), $widget_ops );
			
			$this->alt_option_name = 'widget_banner';
			$this->widget_defaults = array(
				'image_url'        => '',
				'alt_text'         => '',
				'title'            => '',
				'promotion_link'   => '',
				'image_title'      => '',
				'text_description' => '',
				'selected_cat'     => 'e20r-show-all-categories',
				'home_page'        => 'on',
				'auto_fit'         => 'on',
				'target'           => '_self',
				'show_on'          => date_i18n( 'Y-m-d', current_time( 'timestamp' ) ),
				'hide_after'       => null,
			);
			
			//This is link to Help Url
			$this->help_url = "https://eighty20results.com/wordpress-plugins/e20r-promotion-banner-images/help/";
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
			add_shortcode( 'e20r_promotion_image', array( $this, 'loadShortcode' ) );
		}
		
		/**
		 * Load styles and scripts needed by the widget
		 */
		public function enqueueScripts() {
			
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_media();
			
			wp_enqueue_script( 'e20r-promotion-banner-images', plugins_url( 'javascript/epbi-widget-admin.js', __FILE__ ), array( 'jquery' ), E20R_PBI_VERSION, true );
			wp_enqueue_style( 'e20r-promotion-banner-images', plugins_url( 'css/epbi-styles-admin.css', __FILE__ ), null, E20R_PBI_VERSION, 'all' );
			
		}
		
		/**
		 * Display the widget
		 *
		 * @param array $args
		 * @param mixed $instance
		 *
		 * @see WP_Widget::widget
		 */
		public function widget( $args, $instance ) {
			
			global $post;
			
			$home_page = 'off';
			$title     = null;
			$target    = null;
			$image_url = null;
			$auto_fit  = 'off';
			
			$before_widget    = null;
			$after_widget     = null;
			$before_title     = null;
			$after_title      = null;
			$selected_cat     = null;
			$promotion_link   = null;
			$image_title      = null;
			$alt_text         = null;
			$text_description = null;
			
			$cat1 = null;
			$cat2 = null;
			$cat3 = null;
			$cat4 = null;
			$cat5 = null;
			$cat6 = null;
			
			extract( $args );
			
			$widget_options = wp_parse_args( $instance, $this->widget_defaults );
			
			extract( $widget_options, EXTR_OVERWRITE );
			
			error_log('Home Page: ' . print_r( $home_page, true ));
			
			$cat1 = ( is_home() && ( $home_page == 'on' ) );
			$cat2 = ( ( is_category() || is_single() || is_page() ) && $selected_cat == 'e20r-show-all-categories' );
			$cat3 = ( is_home() && $selected_cat == 'e20r-home-only' );
			$cat4 = ( is_single() && in_category( $selected_cat, $post->ID ) );
			$cat5 = ( is_category( $selected_cat ) );
			$cat6 = is_page( $selected_cat );
			
			if ( get_category_by_slug( $selected_cat ) ) {
				$exp = get_category_by_slug( $selected_cat )->cat_name . " Category";
			} else if ( $selected_cat == "e20r-home-only" ) {
				$exp = "Show on Homepage only";
			} else if ( $selected_cat == "e20r-show-all-categories" ) {
				$exp = "Show on all categories";
			}
			
			if ( is_numeric( $selected_cat ) ) {
				$exp = get_the_title( $selected_cat ) . " (#{$selected_cat})";
			}
			
			$timezone = get_option( 'timezone_string' );
			
			if ( empty( $timezone ) ) {
				$timezone = get_option( 'gmt_offset' );
			}
			
			// Get the local times (for the server)
			$show_after = ! empty( $show_on ) ? strtotime( "$show_on 00:00:00 {$timezone}", current_time( 'timestamp' ) ) : null;
			$hide_as_of = ! empty( $hide_after ) ? strtotime( "{$hide_after} 23:59:59 {$timezone}", current_time( 'timestamp' ) ) : null;
			
			// Exit because the start time hasn't passed
			if ( ! empty( $show_after ) && current_time( 'timestamp' ) < $show_after ) {
				error_log( "Hiding. Not after the required time: {$show_after}" );
				
				return;
			}
			
			// Exit because the end time has passed
			if ( ! empty( $hide_as_of ) && current_time( 'timestamp' ) > $hide_as_of ) {
				error_log( "Hiding (timeout). Is after the max time: {$hide_as_of}" );
				
				return;
			}
			
			if ( $cat1 || $cat2 || $cat3 || $cat4 || $cat5 || $cat6 ) {
				echo $before_widget; ?>
				
				<?php if ( ! empty( $title ) ) {
					printf( '%1$s%2$s%3$s', $before_title, esc_html( $title ), $after_title );
					
				} ?>
				
				<?php if ( ! empty( $promotion_link ) ) {
					printf(
						'<a href="%1$s" target="%2$s">',
						esc_url_raw( $promotion_link ),
						esc_attr( $target )
					);
				}
				
				printf(
					'<img src="%1$s" alt="%2$s" title="%3$s>" class="banner-image" %4$s />',
					esc_url_raw( $image_url ),
					esc_html( $alt_text ),
					esc_html( $image_title ),
					( 'on' === $auto_fit ) ? 'style="width: 100%;"' : null
				);
				
				if ( ! empty( $promotion_link ) ) {
					printf( '</a>' );
				} ?>
                <!-- /Ads Image Banner Widget Plugin -->
				<?php
				printf( '<p id="text_description">' );
				printf( '%s', wp_kses_post( $text_description ) );
				printf( '</p>' );
				
				printf( '%s', $after_widget );
			} else {
				// Only for an administrator
				if ( current_user_can( 'administrator' ) ) {
					
					printf( '%s', $before_widget );
					
					if ( 'off' === $home_page ) {
						printf(
							__( 'Display on the home page is %sdisabled%s in the %sAdvanced Settings%s for this widget', 'e20r-promotion-banner-images' ),
							'<strong>', '</strong>', '<em>', '</em>'
						);
					}
					
					printf( '%s', $after_widget );
				}
			}
		}
		
		/**
		 * Process settings when saving them in the widget
		 *
		 * @param array $new_instance
		 * @param array $old_instance
		 *
		 * @return array
		 *
		 * @see WP_Widget::update
		 */
		public function update( $new_instance, $old_instance ) {
			
			error_log( "Saving: " . print_r( $new_instance, true ) );
			
			// Change to 'off' for settings
			if ( ! isset( $new_instance['home_page'] ) || $new_instance['home_page'] == false ) {
				$new_instance['home_page'] = 'off';
			}
			if ( ! isset( $new_instance['auto_fit'] ) || $new_instance['auto_fit'] == false ) {
				$new_instance['auto_fit'] = 'off';
			}
			
			return $new_instance;
		}
		
		/**
		 * Displays the widget in the Widgets are to capture required settings.
		 *
		 * @param array $instance
		 *
		 * @return mixed
		 */
		public function form( $instance ) {
			
			$home_page        = 'off';
			$auto_fit         = 'off';
			$title            = null;
			$show_on          = date_i18n( 'Y-m-d', current_time( 'timestamp' ) );
			$hide_after       = null;
			$promotion_link   = null;
			$selected_cat     = null;
			$text_description = null;
			$alt_text         = null;
			$image_title      = null;
			$target           = null;
			
			error_log("form instance: " . print_r( $instance, true ));
			
			$options = wp_parse_args( $instance, $this->widget_defaults );
			extract( $options, EXTR_OVERWRITE );
			
			$image_url = ! empty( $instance['image_url'] ) ? $instance['image_url'] : null;
			
			$home_page = (bool) ( 'on' === $home_page );
			$auto_fit  = (bool) ( 'on' === $auto_fit );
			?>
            <div class="e20r-promotion-banner-settings">
				<?php
				if ( empty( $image_url ) ) { ?>
                    <p>
                    <div class="e20r-thumb">
                        <div class="e20r-overlay">
                            <span><?php _e( "Preview", "e20r-promotion-banner-images" ); ?></span>
                        </div>

                        <img class="e20r-embedded-img" src="<?php echo esc_url( $image_url ); ?>"/>
                    </div>
                    </p>
					<?php
				} ?>

                <p>
					<?php
					printf( '<label for="%1$s">%2$s</label>',
						esc_html( $this->get_field_id( 'title' ) ),
						__( 'Enter Title of Banner:', 'e20r-promotion-banner-images' )
					);
					
					printf(
						'<input class="widefat" id="%1$s>" name="%2$s" type="text" value="%3$s"/>',
						esc_html( $this->get_field_id( 'title' ) ),
						esc_attr( $this->get_field_name( 'title' ) ),
						esc_html( $title )
					);
					
					printf(
						'<small>%s</small>',
						__( "Can be blank, or any title you'd like", 'e20r-promotion-banner-images' )
					);
					?>
                </p>

                <p id="e20r-promotion-banner-image">
					<?php
					$img_banner_id = $this->get_field_id( 'image_url' );
					
					printf(
						'<label for="%1$s">%2$s</label>',
						esc_attr( $img_banner_id ),
						__( 'Enter URL of Image:', 'e20r-promotion-banner-images' )
					);
					printf(
						'<input class="widefat" id="%1$s" name="%2$s" type="text" value="%3$s"/>',
						esc_attr( $this->get_field_id( 'image_url' ) ),
						esc_attr( $this->get_field_name( 'image_url' ) ),
						esc_url_raw( $image_url )
					);
					printf( '<button class="upload_image_button button button-primary">%1$s</button>',
						__( 'Select/Upload Image', 'e20r-promotion-banner-images' ) )
					?>
                </p>
                <p>
					<?php
					printf(
						'<label for="%1$s">%2$s</label>',
						esc_attr( $this->get_field_id( 'show_on' ) ),
						__( 'Show on/after:', 'e20r-promotion-banner-images' )
					);
					printf(
						'<input class="widefat" id="%1$s" name="%2$s" type="date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="%3$s"/>',
						esc_attr( $this->get_field_id( 'show_on' ) ),
						$this->get_field_name( 'show_on' ),
						esc_attr( $show_on )
					);
					
					printf(
						'<small>%s</small>',
						sprintf(
							__( 'At midnight in the timezone configured in %sthe settings%s', 'e20r-promotion-banner-images' ),
							sprintf( '<a href="%s">', admin_url( 'options-general.php' ) ),
							'</a>' )
					);
					?>
                </p>

                <p>
					<?php
					printf(
						'<label for="%1$s">%2$s</label>',
						esc_attr( $this->get_field_id( 'hide_after' ) ),
						__( 'Hide after:', 'e20r-promotion-banner-images' )
					);
					printf(
						'<input class="widefat" id="%1$s" name="%2$s" type="date" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="%3$s"/>',
						$this->get_field_id( 'hide_after' ),
						$this->get_field_name( 'hide_after' ),
						esc_attr( $hide_after )
					);
					printf(
						'<small>%1$s</small>',
						sprintf(
							__( 'At midnight in the timezone configured in %sthe settings%s', 'e20r-promotion-banner-images' ),
							sprintf( '<a href="%s">', admin_url( 'options-general.php' ) ),
							'</a>' )
					);
					?>
                </p>

                <p>
					<?php
					printf(
						'<label for="%1$s">%2$s</label>',
						esc_attr( $this->get_field_id( 'promotion_link' ) ),
						__( 'Banner Links To:', 'e20r-promotion-banner-images' )
					);
					
					printf(
						'<input class="widefat" id="%1$s" name="%2$s" type="url" value="%3$s" />',
						$this->get_field_id( 'promotion_link' ),
						$this->get_field_name( 'promotion_link' ),
						esc_url( $promotion_link )
					);
					
					printf( '<small>%1$s</small>',
						__( "A link to a page or post on this or any other site", 'e20r-promotion-banner-images' )
					);
					?>
                </p>

                <p>
					<?php
					$options   = array();
					$options[] = sprintf(
						'<option value="e20r-show-all-categories" %1$s>%2$s</option>',
						selected( 'e20r-show-all-categories', $selected_cat, false ),
						__( 'All categories', 'e20r-promotion-banner-images' )
					);
					$options[] = sprintf(
						'<option value="e20r-home-only" %1$s>>%2$s</option>',
						selected( 'e20r-home-only', $selected_cat, false ),
						__( 'Homepage only', 'e20r-promotion-banner-images' )
					);
					
					$categories = get_categories( '' );
					
					foreach ( $categories as $category ) {
						
						$options[] = sprintf(
							'<option value="%1$s" %2$s>%3$s</option>',
							$category->category_nicename,
							selected( $category->category_nicename, $selected_cat, false ),
							$category->cat_name
						);
					}
					$select_options = implode( "\n", $options );
					
					$page_args = array(
						'depth'                 => 0,
						'child_of'              => 0,
						'selected'              => 0,
						'echo'                  => 1,
						'name'                  => 'page_id',
						'id'                    => '',
						'show_option_none'      => '',
						'show_option_no_change' => '',
						'option_none_value'     => '',
					);
					$pages     = get_pages( $page_args );
					
					// Add published page(s)
					$select_options .= walk_page_dropdown_tree( $pages, 0, $page_args );
					
					// Show label for drop-down
					printf(
						'<label for="%1$s">%2$s</label>',
						$this->get_field_id( 'selected_cat' ),
						__( 'Display for/on:', 'e20r-promotion-banner-images' )
					);
					
					// Generate category & page drop-down
					printf( '<select class="e20r-category-list" name="%1$s" id="%2$s">',
						$this->get_field_name( 'selected_cat' ),
						$this->get_field_id( 'selected_cat' )
					);
					printf( '%s', $select_options );
					printf( '</select>' );
					?>
                </p>

                <p>
					<?php
					printf( '<label for="%1$s">%2$s</label>',
						$this->get_field_id( 'text_description' ),
						__( 'Enter Description:', 'e20r-promotion-banner-images' )
					);
					printf( '<textarea class="textarea" rows="7" cols="28" id="%1$s" name="%2$s">%3$s</textarea>',
						$this->get_field_id( 'text_description' ),
						$this->get_field_name( 'text_description' ),
						trim( esc_textarea( $text_description ) )
					);
					?>
                </p>
                <p class="form-allowed-tags">
					<?php printf(
						__( 'You may use these %1$sHTML%2$s tags and attributes', 'e20r-promotion-banner-images' ),
						'<abbr title="HyperText Markup Language">',
						'</abbr>'
					);
					printf(
						'%1$s  &lt;a href="" title=""&gt; &lt;abbr title=""&gt; &lt;acronym title=""&gt; &lt;b&gt; &lt;blockquote cite=""&gt; &lt;cite&gt; &lt;code&gt; &lt;del datetime=""&gt; &lt;em&gt; &lt;i&gt; &lt;q cite=""&gt; &lt;strike&gt; &lt;strong&gt;%2$s',
						'<code>',
						'</code>'
					);
					?>
                </p>
                <p>
					<?php
                    error_log( "Home page: {$home_page}: " . ( $home_page ? 'Checked' : "Unchecked" ) );
                    
                    
					printf( '<input type="checkbox" class="checkbox" id="%1$s" name="%2$s" %3$s />',
						$this->get_field_id( 'home_page' ),
						$this->get_field_name( 'home_page' ),
						checked( 1, $home_page, false )
					);
					printf( '<label for="%1$s">%2$s</label>',
						$this->get_field_id( 'home_page' ),
						__( 'Allow on home page', 'e20r-promotion-banner-images' )
					);
					?>
                </p>

                <p>
					<?php
					error_log( "Fit to column: " . ( $auto_fit ? 'Checked' : "Unchecked" ) );
					printf( '<input type="checkbox" class="checkbox" id="%1$s" name="%2$s" %3$s />',
						$this->get_field_id( 'auto_fit' ),
						$this->get_field_name( 'auto_fit' ),
						checked( $auto_fit, 1, false )
					);
					printf( '<label for="%1%s">%2$s</label>',
						$this->get_field_id( 'auto_fit' ),
						__( 'Fit to column width', 'e20r-promotion-banner-images' )
					);
					?>
                </p>

                <div class="e20r-advanced-toggle">
                    <span onclick="jQuery(this).next('div').slideToggle();" class="expander">
                        <?php printf( __( 'Click: %sAdvanced Config%s (show/hide)', 'e20r-promotion-banner-images' ), '<strong>', '</strong>' ); ?>
                    </span>
                    <div class="e20r-advanced-options" style="display:none;">

                        <p>
							<?php
							printf(
								'<label for="%1$s">%2$s</label>',
								$this->get_field_id( 'alt_text' ),
								__( 'Image Alt Text:', 'e20r-promotion-banner-images' )
							);
							printf( '<input class="widefat" id="%1$s" name="%2$s" type="text" value="%3$s" />',
								$this->get_field_id( 'alt_text' ),
								$this->get_field_name( 'alt_text' ),
								esc_html( $alt_text )
							);
							?>
                        </p>

                        <p>
							<?php
							printf( '<label for="%1%s">%2$s</label>',
								$this->get_field_id( 'image_title' ),
								__( 'Image Title:', 'e20r-promotion-banner-images' )
							);
							printf( '<input class="widefat" id="%1$s" name="%2$s" type="text" value="%3$s" />',
								$this->get_field_id( 'image_title' ),
								$this->get_field_name( 'image_title' ),
								esc_html( $image_title )
							);
							?>
                        </p>

                        <p>
							<?php
							printf( '<label for="%1$s">%2$s</label>',
								$this->get_field_id( 'target' ),
								__( 'Link target:', 'e20r-promotion-banner-images' )
							);
							
							$select_options = array(
								'_self'  => __( 'Current frame', 'e20r-promotion-banner-images' ),
								'_blank' => __( 'New page/tab', 'e20r-promotion-banner-images' ),
								'_top'   => __( 'Top frame', 'e20r-promotion-banner-images' ),
							);
							$options        = array();
							
							foreach ( $select_options as $ft => $label ) {
								$options[] = sprintf(
									'<option value="%1$s" %2$s>%3$s</option>',
									$ft,
									selected( $ft, $target, false ),
									$label
								);
							}
							
							printf( '<select name="%1$s" id="%2$s">',
								$this->get_field_name( 'target' ),
								$this->get_field_id( 'target' )
							);
							printf( "%s", implode( "\n", $options ) );
							printf( '</select>' );
							?>
                        </p>

                    </div>
                </div>
                <div class="clear"></div>
            </div>
			<?php
		}
		
		/**
		 * Generate the help link for the widget
		 *
		 * @param string $key
		 * @param string $text
		 */
		public function help_link( $key, $text = '(?)' ) {
			printf( '<a href="%1$s#%2$s" target="_blank" class="help-link">%3$s</a>', $this->help_url, $key, $text );
		}
		
		/**
		 * Process the widget as a shortcode
		 *
		 * @param array $atts
		 *
		 * @return string
		 */
		public function loadShortcode( $atts ) {
			
			// global $wp_registered_widgets, $wp_registered_sidebars, $sidebars_widgets;
			
			extract(
				shortcode_atts(
					array(
						'image_url'        => '',
						'alt_text'         => '',
						'title'            => '',
						'image_link'       => '',
						'image_title'      => '',
						'text_description' => '',
						'home_page'        => 'on',
						'auto_fit'         => 'on',
						'target'           => '_self',
						'show_on'          => date_i18n( 'Y-m-d', current_time( 'timestamp' ) ),
						'hide_after'       => null,
					),
					$atts
				)
			);
			
			register_widget( 'EBPI' );
			
			$presentation = array(
				'before_widget' => '<div class="box widget">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="widget-title">',
				'after_title'   => '</div>',
			);
			
			ob_start();
			the_widget( 'EBPI', $presentation, $atts );
			
			return ob_get_clean();
		}
		
	} // End of Class
	
}
/**
 * Initiate this widget
 **/
add_action( 'widgets_init', create_function( '', 'return register_widget( new E20R\Promotion_Banners\EBPI() );' ) );

// Load one-click update support for this BETA from a custom repository
if ( file_exists( plugin_dir_path( __FILE__ ) . "includes/plugin-updates/plugin-update-checker.php" ) ) {
	
	require_once( plugin_dir_path( __FILE__ ) . "includes/plugin-updates/plugin-update-checker.php" );
	
	$plugin_updates = \PucFactory::buildUpdateChecker(
		'https://eighty20results.com/protected-content/e20r-promotion-banner-images/metadata.json',
		__FILE__,
		'e20r-promotion-banner-images'
	);
}
