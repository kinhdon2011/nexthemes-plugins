<?php

	
if ( ! class_exists( 'NTH_List_Grid' ) ) {

	class NTH_List_Grid {

		public function __construct() {
		
			add_action( 'wp' , array( $this, 'setup_gridlist' ) , 20);

			// Init settings
			$this->settings = array(
				array(
					'name' 	=> __( 'Default catalog view', 'nexthemes-plugin' ),
					'type' 	=> 'title',
					'id' 	=> 'wc_glt_options'
				),
				array(
					'name' 		=> __( 'Default catalog view', 'nexthemes-plugin' ),
					'desc_tip' 	=> __( 'Display products in grid or list view by default', 'nexthemes-plugin' ),
					'id' 		=> 'wc_glt_default',
					'type' 		=> 'select',
					'options' 	=> array(
						'grid'  => __( 'Grid', 'nexthemes-plugin' ),
						'list' 	=> __( 'List', 'nexthemes-plugin' ),
						'table' => __( 'Table', 'nexthemes-plugin' )
					)
				),
				array( 'type' => 'sectionend', 'id' => 'wc_glt_options' ),
			);

			// Default options
			add_option( 'wc_glt_default', 'grid' );

			// Admin
			add_action( 'woocommerce_settings_image_options_after', array( $this, 'admin_settings' ), 20 );
			add_action( 'woocommerce_update_options_catalog', array( $this, 'save_admin_settings' ) );
			add_action( 'woocommerce_update_options_products', array( $this, 'save_admin_settings' ) );
		}

		/*-----------------------------------------------------------------------------------*/
		/* Class Functions */
		/*-----------------------------------------------------------------------------------*/

		function admin_settings() {
			woocommerce_admin_fields( $this->settings );
		}

		function save_admin_settings() {
			woocommerce_update_options( $this->settings );
		}

		// Setup
		function setup_gridlist() {
			if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
				//add_action( 'wp_enqueue_scripts', array( $this, 'setup_scripts_styles' ), 20);
				add_action( 'wp_enqueue_scripts', array( $this, 'setup_scripts_script' ), 20);
				add_action( 'woocommerce_before_shop_loop', array( $this, 'gridlist_toggle_button' ), 10);
				//add_action( 'woocommerce_after_shop_loop_item', array( $this, 'gridlist_buttonwrap_open' ), 9);
				//add_action( 'woocommerce_after_shop_loop_item', array( $this, 'gridlist_buttonwrap_close' ), 11);
				//add_action( 'woocommerce_after_shop_loop_item', array( $this, 'gridlist_hr' ), 30);
				//add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_single_excerpt', 5);
				add_action( 'woocommerce_after_subcategory', array( $this, 'gridlist_cat_desc' ) );
				
				add_filter('nth_woocommerce_product_loop_class_filter', array($this, 'product_loop_class_filter'), 10, 1);
			}
		}

		function setup_scripts_script() {
			add_action( 'wp_footer', array( $this, 'gridlist_set_default_view' ) );
		}

		// Toggle button
		function gridlist_toggle_button() {
			?>
				<nav class="gridlist-toggle">
					<a href="#" id="grid" data-toggle="tooltip" title="<?php _e('Grid view', 'nexthemes-plugin' ); ?>"><span class="fa fa-th"></span> <em><?php _e( 'Grid view', 'nexthemes-plugin' ); ?></em></a><a href="#" id="list" data-toggle="tooltip" title="<?php _e('List view', 'nexthemes-plugin'); ?>"><span class="fa fa-th-list"></span> <em><?php _e( 'List view', 'nexthemes-plugin' ); ?></em></a><a href="#" id="table" data-toggle="tooltip" title="<?php _e('Table view', 'nexthemes-plugin'); ?>"><span class="fa fa-table"></span> <em><?php _e( 'Table view', 'nexthemes-plugin' ); ?></em></a>
				</nav>
			<?php
		}

		// Button wrap
		function gridlist_buttonwrap_open() {
			?>
				<div class="gridlist-buttonwrap">
			<?php
		}
		function gridlist_buttonwrap_close() {
			?>
				</div>
			<?php
		}

		// hr
		function gridlist_hr() {
			?>
				<hr />
			<?php
		}

		function gridlist_set_default_view() {
			$default = get_option( 'wc_glt_default' );
			
			?>
				<script type="text/javascript">
					/* <![CDATA[ */
					if (jQuery.cookie( 'gridcookie' ) == null) {
						jQuery( '.gridlist-toggle #<?php echo $default; ?>' ).addClass( 'active' );
					}
					/* ]]> */
				</script>
			<?php
		}
		
		function product_loop_class_filter( $classes ){
			$default = get_option( 'wc_glt_default' );
			$classes[] = 'nth_grid_list_toggle';
			if( strlen( trim($default) ) > 0 ) $classes[] = $default;
			return $classes;
		}

		function gridlist_cat_desc( $category ) {
			global $woocommerce;
			echo '<div itemprop="description">';
				echo $category->description;
			echo '</div>';

		}
	}

	$NTH_List_Grid = new NTH_List_Grid();
}

