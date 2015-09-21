<?php
/**
 * @package nth-portfolios
 */

if( !class_exists( 'NTH_Portfolio_Admin' ) ) {
	
	class NTH_Portfolio_Admin extends NTH_Portfolio {
		
		function __construct(){
			parent::__construct();
			self::init();
		}
		
		public function init(){
			add_filter('manage_'.$this->post_type.'_posts_columns', array( $this, 'tableHeading' ) );
			add_action( 'manage_'.$this->post_type.'_posts_custom_column', array( $this,'tableContent' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'registerScript' ) );
		}
		
		public function registerScript(){
			if( strcmp( trim(get_post_type(get_the_ID())), trim( $this->post_type ) ) == 0 ) {
				wp_register_style( 'nth-portfolio-admin', NTH_PLUGIN_URL . 'assets/css/nth-portfolio-admin.css', false, NTH_VERSION );
				wp_enqueue_style( 'nth-portfolio-admin' );
			}
			
		}
		
		public function tableHeading( $columns = array() ){
			if( count($columns) == 0  ) return;
			$columns = array_merge(array( 'cb' => "", 'nth_thumb' => "<span class=\"dashicons dashicons-format-image\"> </span>" ), $columns);
			return $columns;
		}
		
		public function tableContent( $column_name, $post_id ){
			if( strcmp( trim($column_name), 'nth_thumb' ) == 0 ): 
				if( has_post_thumbnail() ): ?>
					<a href="<?php echo get_edit_post_link();?>">
					<?php the_post_thumbnail('thumbnail'); ?>
					</a>
				<?php else: 
					echo "—";
				endif;
			endif;
		}
		
		
	}
	global $nth_portfolio_admin;
	$nth_portfolio_admin = new NTH_Portfolio_Admin();
}
