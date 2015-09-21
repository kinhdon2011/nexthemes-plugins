<?php 
// ! File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'NTH_Woo_Shortcodes' ) ) : 

class NTH_Woo_Shortcodes {
	
	private static $classes = array('nth-shortcode', 'woocommerce');
	
	private static $custom_css = '', $custom_handle = '';
	
	public static function get_template( $template_name, $args = array(), $products = null ){
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}
		
		$located = NTH_PLUGIN_TMPL_DIR . $template_name;
		
		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );
			return;
		}
		
		include( $located );
	}
	
	public static function ajax_call(){
		add_action( 'wp_ajax_nth_woo_get_product_by_cat' , array( __CLASS__, 'woo_get_product_by_cat' ) );
		add_action( 'wp_ajax_nopriv_nth_woo_get_product_by_cat', array( __CLASS__, 'woo_get_product_by_cat' ) );
	}
	
	public static function shortcodeArgs(){
		self::ajax_call();
		return array(
			'nth_top_rated_products'	=> __CLASS__ . '::top_rated_products',
			'nth_best_selling_products'	=> __CLASS__ . '::best_selling_products',
			'nth_recent_products'		=> __CLASS__ . '::recent_products',
			'nth_sale_products'			=> __CLASS__ . '::sale_products',
			'nth_products_category'		=> __CLASS__ . '::products_category',
			'nth_products_cats_tabs'	=> __CLASS__ . '::products_categories_tabs',
			'nth_featured_products'		=> __CLASS__ . '::featured_products',
			'nth_products'				=> __CLASS__ . '::products',
			'nth_product_tags'			=> __CLASS__ . '::product_tags',
			'nth_product_cats'			=> __CLASS__ . '::product_cats',
		);
	}
	
	public static function products( $atts ){
		global $woocommerce_loop;
		
		if ( empty( $atts ) ) return '';
		
		$atts = shortcode_atts( array(
			"title" 		=> '',
			"item_style"	=> 'grid',
			"as_widget"		=> '0',
			"is_slider"		=> '0',
			"excerpt_limit" => 10,
			'columns' 		=> '4',
			'orderby' 		=> 'title',
			'order'   		=> 'asc',
			'ids'     		=> '',
			'skus'    		=> ''
		), $atts );
		
		$meta_query = WC()->query->get_meta_query();
		
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $atts['orderby'],
			'order'               => $atts['order'],
			'posts_per_page'      => -1,
			'meta_query'          => $meta_query
		);
		
		if ( ! empty( $atts['skus'] ) ) {
			$skus = explode( ',', $atts['skus'] );
			$skus = array_map( 'trim', $skus );
			$args['meta_query'][] = array(
				'key' 		=> '_sku',
				'value' 	=> $skus,
				'compare' 	=> 'IN'
			);
		}
		
		if ( ! empty( $atts['ids'] ) ) {
			$ids = explode( ',', $atts['ids'] );
			$ids = array_map( 'trim', $ids );
			$args['post__in'] = $ids;
		}
		
		ob_start();
		
		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
		
		$old_columns = $woocommerce_loop['columns'];
		
		if ( $products->have_posts() ) :
		
			self::get_template( 'shortcode-woo-nomal.tpl.php', $atts, $products );
			
		endif;
		
		wp_reset_postdata();
		
		$woocommerce_loop['columns'] = $old_columns;
		
		$classes = self::pareShortcodeClass( 'columns-' . absint( $atts['columns'] ) );
		
		return '<div class="'.esc_attr( implode( ' ', $classes ) ).'">' . ob_get_clean() . '</div>';
	}
	
	public static function sale_products( $atts ) {
		global $woocommerce_loop, $woocommerce;
		
		$atts = shortcode_atts(array(
			"title" 		=> '',
			"item_style"	=> 'grid',
			"is_slider"		=> '0',
			"per_page"		=> '12',
			"columns"		=> '4',
			"orderby"		=> 'title',
			"order"			=> 'asc',
			"as_widget"		=> 0,
			"excerpt_limit" => 10,
			"hide_free"		=> 0,
			"show_hidden"	=> 0
		),$atts);
		
		$product_ids_on_sale = wc_get_product_ids_on_sale();
		//$meta_query = WC()->query->get_meta_query();
		
		$args = array(
			'posts_per_page'	=> $atts['per_page'],
			'orderby' 			=> $atts['orderby'],
			'order' 			=> $atts['order'],
			'no_found_rows' 	=> 1,
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product',
			'meta_query' 		=> array(),
			'post__in'			=> array_merge( array( 0 ), $product_ids_on_sale )
		);
		
		if( absint($atts['as_widget']) ) {
			if ( !absint($atts['show_hidden']) ) {
				$args['meta_query'][] = WC()->query->visibility_meta_query();
				$args['post_parent']  = 0;
			}
			
			if ( absint( $atts['hide_free'] ) ) {
				$args['meta_query'][] = array(
					'key'     => '_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'DECIMAL',
				);
			}
		} else {
			$args['meta_query'] = WC()->query->get_meta_query();
		}
		
		ob_start();
		
		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
		
		$old_columns = $woocommerce_loop['columns'];
		
		if ( $products->have_posts() ) : 
		
			self::get_template( 'shortcode-woo-nomal.tpl.php', $atts, $products );
		
		endif;
		
		wp_reset_postdata();
		
		$woocommerce_loop['columns'] = $old_columns;
		
		$classes = self::pareShortcodeClass( 'columns-' . absint( $atts['columns'] ) );
		
		return '<div class="'.esc_attr(implode(' ', $classes)).'">' . ob_get_clean() . '</div>';
	}
	
	public static function products_category( $atts ){
		global $woocommerce_loop;
		
		$atts = shortcode_atts( array(
			"title" 		=> '',
			"item_style"	=> 'grid',
			"as_widget"		=> '0',
			"per_page"		=> '12',
			"is_slider"		=> '0',
			"excerpt_limit" => 10,
			"columns"		=> '4',
			"orderby"		=> 'date',
			"order"			=> 'desc',
			"category" 		=> '',
			"hide_free"		=> 0,
			"show_hidden"	=> 0
		), $atts );
		
		$ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );
		$meta_query    = WC()->query->get_meta_query();
		
		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $ordering_args['orderby'],
			'order' 				=> $ordering_args['order'],
			'posts_per_page' 		=> $atts['per_page'],
			'meta_query' 			=> $meta_query,
			'tax_query' 			=> array(
				array(
					'taxonomy' 		=> 'product_cat',
					'terms' 		=> array_map( 'sanitize_title', explode( ',', $atts['category'] ) ),
					'field' 		=> 'slug',
					'operator' 		=> 'IN'
				)
			)
		);
		
		if ( isset( $ordering_args['meta_key'] ) ) {
			$args['meta_key'] = $ordering_args['meta_key'];
		}
		
		ob_start();
		
		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
		
		if( absint($atts['as_widget']) ) {
			if( strcmp( $atts['item_style'], 'grid' ) == 0 ) {
				$woocommerce_loop['columns'] = 2;
			} else {
				$woocommerce_loop['columns'] = 1;
			}
		} else $woocommerce_loop['columns'] = $atts['columns'];
		
		if ( $products->have_posts() ) : 
			
			self::get_template( 'shortcode-woo-nomal.tpl.php', $atts, $products );
			
		endif;
		
		wp_reset_postdata();
		
		$classes = self::pareShortcodeClass( 'columns-' . absint( $atts['columns'] ) );
		
		if(strlen($atts['title'])>0 && absint($atts['as_widget'])) $classes[] = 'widget_boxed';
		
		return '<div class="'.esc_attr( trim(implode(' ', $classes)) ).'">' . ob_get_clean() . '</div>';
	}
	
	public static function recent_products( $atts ){
		global $woocommerce_loop;
		
		$atts = shortcode_atts(array(
			"title" 		=> '',
			"item_style"	=> 'grid',
			"as_widget"		=> '0',
			"is_slider"		=> '0',
			"excerpt_limit" => 10,
			"per_page"		=> '12',
			"columns"		=> '4',
			"orderby"		=> 'date',
			"order"			=> 'desc',
			"hide_free"		=> 0,
			"show_hidden"	=> 0
		),$atts);
		
		$args = array(
			'post_type'				=> 'product',
			'post_status'			=> 'publish',
			'no_found_rows'			=> 1,
			'posts_per_page' 		=> $atts['per_page'],
			'orderby' 				=> $atts['orderby'],
			'order' 				=> $atts['order'],
			'meta_query' 			=> array()
		);
		
		if ( !absint($atts['show_hidden']) ) {
			$args['meta_query'][] = WC()->query->visibility_meta_query();
			$args['post_parent']  = 0;
		}
		
		if ( absint( $atts['hide_free'] ) ) {
			$args['meta_query'][] = array(
				'key'     => '_price',
				'value'   => 0,
				'compare' => '>',
				'type'    => 'DECIMAL',
			);
		}
		
		ob_start();
		
		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
		
		$old_columns = $woocommerce_loop['columns'];
		
		if ( $products->have_posts() ) : 
			
			self::get_template( 'shortcode-woo-nomal.tpl.php', $atts, $products );
			
		endif;
		
		wp_reset_postdata();
		
		$woocommerce_loop['columns'] = $old_columns;
		
		$classes = self::pareShortcodeClass( 'columns-' . absint( $atts['columns'] ) );
		
		return '<div class="'.esc_attr( implode( ' ', $classes ) ).'">' . ob_get_clean() . '</div>';
	}
	
	public static function top_rated_products( $atts ){
		global $woocommerce_loop;
		
		$atts = shortcode_atts(array(
			"title" 		=> '',
			"item_style"	=> 'grid',
			"as_widget"		=> '0',
			"is_slider"		=> '0',
			"excerpt_limit" => 10,
			"per_page"		=> '12',
			"columns"		=> '4',
			"orderby"		=> 'date',
			"order"			=> 'desc',
		),$atts);
		
		extract( $atts );
		
		$meta_query = WC()->query->get_meta_query();
		
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $atts['orderby'],
			'order'               => $atts['order'],
			'posts_per_page'      => $atts['per_page'],
			'meta_query'          => $meta_query
		);
		
		ob_start();
		
		add_filter( 'posts_clauses', array( __CLASS__, 'order_by_rating_post_clauses' ) );
		
		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
		
		remove_filter( 'posts_clauses', array( __CLASS__, 'order_by_rating_post_clauses' ) );
		
		$old_columns = $woocommerce_loop['columns'];
		
		if ( $products->have_posts() ) : 
		
			self::get_template( 'shortcode-woo-nomal.tpl.php', $atts, $products );
			
		endif;
		
		wp_reset_postdata();
		
		$woocommerce_loop['columns'] = $old_columns;
		
		$classes = self::pareShortcodeClass( 'columns-' . absint( $atts['columns'] ) );
		
		return '<div class="'.esc_attr( implode( ' ', $classes ) ).'">' . ob_get_clean() . '</div>';
	}
	
	
	public static function best_selling_products( $atts ){
		global $woocommerce_loop;
		$atts = shortcode_atts(array(
			"title" 		=> '',
			"item_style"	=> 'grid',
			"as_widget"		=> '0',
			"is_slider"		=> '0',
			"excerpt_limit" => 10,
			"per_page"		=> '12',
			"columns"		=> '4',
		),$atts);
		
		$meta_query = WC()->query->get_meta_query();
		
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts['per_page'],
			'meta_key'            => 'total_sales',
			'orderby'             => 'meta_value_num',
			'meta_query'          => $meta_query
		);
		
		ob_start();
		
		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
		
		$old_columns = $woocommerce_loop['columns'];
		
		if ( $products->have_posts() ) : 
		
			self::get_template( 'shortcode-woo-nomal.tpl.php', $atts, $products );
			
		endif;
		
		wp_reset_postdata();
		
		$woocommerce_loop['columns'] = $old_columns;
		
		$classes = self::pareShortcodeClass( 'columns-' . absint( $atts['columns'] ) );
		
		return '<div class="'.esc_attr( implode( ' ', $classes ) ).'">' . ob_get_clean() . '</div>';
	}
	
	public static function product_tags( $atts ){
		extract(shortcode_atts( array(
			'title'		=> '',
			'tags' 		=> '',
			't_color'	=> '#333333'
		), $atts ));
		
		if( strlen( trim( $tags ) ) == 0 ) return;
		
		$tags = explode( ',', $tags );
		
		ob_start();
		
		?>
		<div class="nth-shortcode nth-product-taxs-wrapper nth-product-categories-wrapper">
			<?php if( strlen( $title ) > 0 ): ?>
			<span style="color: <?php echo esc_attr( $t_color );?>"><?php echo esc_attr( $title );?></span>:
			<?php endif;?>
			
			<?php foreach( $tags as $tag ): ?>
				
				<?php if($term_tag = get_term_by( 'slug', $tag, "product_tag" )): ?>
				
					<a href="<?php echo esc_url( get_term_link( $term_tag->term_id, "product_tag" ) );?>"><?php echo esc_attr( $term_tag->name );?></a>
				
				<?php endif;?>
			
			<?php endforeach;?>
			
		</div><!-- .nth-product-categories-wrapper -->
		
		<?php 
		
		wp_reset_postdata();
		
		return ob_get_clean();
	}
	
	public static function product_cats( $atts ){
		extract(shortcode_atts( array(
			'title'			=> '',
			'cats' 			=> '',
			'hover_color'	=> '#333333'
		), $atts ));
		
		if( strlen( trim( $cats ) ) == 0 ) return;
		
		$cats = explode( ',', $cats );
		
		$args = array(
			'slug' => $cats,
			'orderby'	=> 'name',
			'order'		=> 'ASC',
			'hide_empty'	=> false,
			'cache_domain'	=> 'nth_woo'
		);
		
		ob_start();
		
		$terms = get_terms('product_cat', $args);
		
		if( !empty($terms) ):
			$customcss_class = 'nth_shortcodeclass_' . rand();
			echo '<ul class="product-cats '.$customcss_class.'">';
			foreach( $terms as $term ) {
				
				$link = get_permalink($term->term_id);
				printf( '<li class="cat-%1$d"><a href="%2$s" title="%3$s">%3$s</a></li>', absint($term->term_id), esc_url($link), esc_attr($term->name) );
				
			}
			echo "</ul>";
			
			?>
				<style type="text/css">
					.<?php echo $customcss_class?> li a:hover {
						color: <?php echo $hover_color;?>;
						font-weight: bold;
					}
				</style>
			<?php 
			
		endif;
		
		wp_reset_postdata();
		
		$classes = self::pareShortcodeClass( __FUNCTION__ );
		
		return '<div class="'.esc_attr( implode( ' ', $classes ) ).'">' . ob_get_clean() . '</div>';
	}
	
	
	public static function featured_products( $atts ){
		global $woocommerce_loop;
		$atts = shortcode_atts( array(
			"title" 		=> '',
			"item_style"	=> 'grid',
			"as_widget"		=> '0',
			"is_slider"		=> '0',
			"excerpt_limit" => 10,
			"per_page"		=> '12',
			"columns"		=> '4',
			"orderby"		=> 'date',
			"order"			=> 'desc',
			"hide_free"		=> 0,
			"show_hidden"	=> 0
		), $atts );
		
		$meta_query   = array();
		$meta_query[] = array(
			'key'   => '_featured',
			'value' => 'yes'
		);
		
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts['per_page'],
			'orderby'             => $atts['orderby'],
			'order'               => $atts['order'],
			'meta_query'          => $meta_query
		);
		
		if( absint($atts['as_widget']) ) {
			if ( !absint($atts['show_hidden']) ) {
				$args['meta_query'][] = WC()->query->visibility_meta_query();
				$args['post_parent']  = 0;
			}
			
			if ( absint( $atts['hide_free'] ) ) {
				$args['meta_query'][] = array(
					'key'     => '_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'DECIMAL',
				);
			}
		} else {
			$args['meta_query'] = WC()->query->get_meta_query();
		}
		
		
		ob_start();
		
		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
		
		$old_columns = $woocommerce_loop['columns'];
		
		if ( $products->have_posts() ) : 
			
			self::get_template( 'shortcode-woo-nomal.tpl.php', $atts, $products );
			
		endif;
		
		wp_reset_postdata();
		
		$woocommerce_loop['columns'] = $old_columns;
		
		$classes = self::pareShortcodeClass( 'columns-' . absint( $atts['columns'] ) );
		
		return '<div class="'.esc_attr( implode( ' ', $classes ) ).'">' . ob_get_clean() . '</div>';
	}
	
	public static function products_categories_tabs( $atts ){
		$atts = shortcode_atts( array(
			"title" 		=> '',
			"item_style"	=> 'grid',
			"as_widget"		=> '0',
			"excerpt_limit"	=> 10,
			"per_page"		=> '12',
			"columns"		=> '4',
			"orderby"		=> 'date',
			"order"			=> 'desc',
			"category" 		=> '',
			"use_ajax"		=> '1'
		), $atts );
		
		extract($atts);
		
		if ( ! $atts['category'] ) return '';
		
		ob_start();
		
		$category = explode( ',', $category );
		
		if( count($category) > 0 ):
		
			$tab_rand = 'tab_item_' . mt_rand();
			$tab_rand2 = 'tab_' . mt_rand(); ?>
			
			<div class="nth_products_categories_shortcode" data-atts="<?php echo esc_attr( json_encode($atts) );?>" id="<?php echo esc_attr($tab_rand2);?>">
				
				<?php 
				$_tabs_li = '';
				$_tabs_content = '';
				$i = 0;
				$first_term = '';
				foreach( $category as $slug ) {
					$term = get_term_by( "slug", $slug, "product_cat" );
					if( $term ) {
						$_class  = absint( $use_ajax ) == 0? 'tab-item': 'tab-item-ajax';
						$_class .= $i == 0? ' active': '';
						
						$_tabs_li .= "<li class=\"{$_class}\">";
						$_tabs_li .= '<a href="javascript:void(0);" data-id="'.esc_attr( $tab_rand . '_' . $term->term_id ).'" data-slug="'.esc_attr($term->slug).'">';
						$_tabs_li .= esc_attr($term->name);
						$_tabs_li .= '</a>';
						$_tabs_li .= "</li>";
						
						$_tabs_content .= '<div class="tab-content-item'.($i !== 0? ' hidden': ' show').'" id="'.esc_attr($tab_rand . '_' . $term->term_id).'">';
						$_tabs_content .= self::get_product_by_cat_content( $atts, $term->slug );
						$_tabs_content .= '</div>';
						if( $i == 0 ) $first_term = $term;
						$i++;
					}
				}
				?>
				
				<div class="nth-shortcode-header">
					<?php if( strlen( $title ) > 0 ):?>
					<h3 class="heading-title"><?php echo esc_attr($title);?></h3>
					<?php endif;?>
					
					<ul class="shortcode-woo-tabs">
						<?php echo $_tabs_li;?>
					</ul>
				</div>
				
				<div class="nth-shortcode-content">
					<?php if(absint($use_ajax) == 0): ?>
					<?php echo $_tabs_content;?>
					<?php else: ?>
					<div class="tab-content-item show ajax-content" id="<?php echo esc_attr($tab_rand) . '_ajax_content'?>">
						<?php echo self::get_product_by_cat_content( $atts, $first_term->slug );?>
					</div>
					<?php endif;?>
				</div>
			
			</div>
			
		<?php 
		$classes = self::pareShortcodeClass( 'columns-' . absint( $columns ) );
		
		return '<div class="'.esc_attr( implode( ' ', $classes ) ).'">' . ob_get_clean() . '</div>';?>
		
		<?php endif;
	}
	
	public static function get_product_by_cat_content( $atts, $slug, $slide_id = '' ){
		global $woocommerce_loop;
		
		$ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );
		$meta_query    = WC()->query->get_meta_query();
		
		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $ordering_args['orderby'],
			'order' 				=> $ordering_args['order'],
			'posts_per_page' 		=> $atts['per_page'],
			'meta_query' 			=> $meta_query,
			'tax_query' 			=> array(
				array(
					'taxonomy' 		=> 'product_cat',
					'terms' 		=> array( $slug ),
					'field' 		=> 'slug',
					'operator' 		=> 'IN'
				)
			)
		);
		
		if ( isset( $ordering_args['meta_key'] ) ) {
			$args['meta_key'] = $ordering_args['meta_key'];
		}
		
		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );
		
		$old_columns = $woocommerce_loop['columns'];
		
		ob_start();
		
		$atts['title'] = '';
		$atts['is_slider']	= 1;
		
		if ( $products->have_posts() ) : 
		
			self::get_template( 'shortcode-woo-nomal.tpl.php', $atts, $products );
		
		endif;
		
		wp_reset_postdata();
		
		$woocommerce_loop['columns'] = $old_columns;
		
		$classes = self::pareShortcodeClass( 'columns-' . absint( $atts['columns'] ) );
		
		return '<div class="'.esc_attr( implode( ' ', $classes ) ).'">' . ob_get_clean() . '</div>';
	}
	
	
	public static function order_by_rating_post_clauses( $args ){
		global $wpdb;
		
		$args['where'] .= " AND $wpdb->commentmeta.meta_key = 'rating' ";
		
		$args['join'] .= "
			LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
			LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
		";
		
		$args['orderby'] = "$wpdb->commentmeta.meta_value DESC";

		$args['groupby'] = "$wpdb->posts.ID";

		return $args;
	}
	
	private static function pareShortcodeClass( $class = '' ){
		$classes = self::$classes;
		if( strlen($class) > 0 )
			$classes[] = $class;
		return $classes;
	}
	
	public static function woo_get_product_by_cat(){
		
		$atts = array(); $sub_cat_slug = ''; $element = '';
			
		if( isset($_POST['atts']) ){
			$atts = $_POST['atts'];
		}
		
		if( isset($_POST['cat_slug']) ){
			$cat_slug = $_POST['cat_slug'];
		}
		
		if( isset($_POST['element']) ){
			$element = $_POST['element'];
		}
		
		$atts = shortcode_atts(array(
			"title" 		=> '',
			"item_style"	=> 'grid',
			"as_widget"		=> '0',
			"per_page"		=> '12',
			"excerpt_limit"	=> 10,
			"columns"		=> '4',
			"orderby"		=> 'date',
			"order"			=> 'desc',
			"category" 		=> '',
			"use_ajax"		=> '1'
		),$atts);
		
		global $woocommerce_loop;
	
		$ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );
		$meta_query    = WC()->query->get_meta_query();
		
		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $ordering_args['orderby'],
			'order' 				=> $ordering_args['order'],
			'posts_per_page' 		=> $atts['per_page'],
			'meta_query' 			=> $meta_query,
			'tax_query' 			=> array(
				array(
					'taxonomy' 		=> 'product_cat',
					'terms' 		=> array( $cat_slug ),
					'field' 		=> 'slug',
					'operator' 		=> 'IN'
				)
			)
		);
		
		if ( isset( $ordering_args['meta_key'] ) ) {
			$args['meta_key'] = $ordering_args['meta_key'];
		}
		
		$products = new WP_Query( $args );
		
		$old_columns = $woocommerce_loop['columns'];
		
		ob_start();
		
		$atts['title'] = '';
		$atts['is_slider']	= 1;
		
		if ( $products->have_posts() ) : 
		
			self::get_template( 'shortcode-woo-nomal.tpl.php', $atts, $products );
		
		endif;
		
		wp_reset_postdata();
		
		$woocommerce_loop['columns'] = $old_columns;
		
		$classes = self::pareShortcodeClass( 'columns-' . absint( $atts['columns'] ) );
		
		echo '<div class="'.esc_attr( implode( ' ', $classes ) ).'">' . ob_get_clean() . '</div>';
		
		die();
	}
	
}

endif;