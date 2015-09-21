<?php 
// ! File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'NTH_Shortcodes' ) ) : 

class NTH_Shortcodes extends NTH_Woo_Shortcodes {
	
	public static function checkPlugin( $path = '' ){
		if( strlen( $path ) == 0 ) return false;
		$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
		if ( in_array( trim( $path ), $_actived ) ) return true;
		else return false;
	}
	
	public static function init() {
		$shortcodes = array(
			'nth_banner' 			=> __CLASS__ . '::banners',
			'nth_brands' 			=> __CLASS__ . '::brands',
			'nth_feedburner' 		=> __CLASS__ . '::feedburner',
			'nth_infobox' 			=> __CLASS__ . '::infobox',
            'nth_recent_comments' 	=> __CLASS__ . '::recent_comments',
			'nth_recent_posts'		=> __CLASS__ . '::recent_posts',
			'nth_pricing'			=> __CLASS__ . '::pricing',
			'nth_action'			=> __CLASS__ . '::action',
			'nth_maps'				=> __CLASS__ . '::google_maps'
		);
		
		if( class_exists( 'NTH_StaticBlock' ) ) {
			$shortcodes['nth_staticblock'] = __CLASS__ . '::staticblock';
		}
		
		if( class_exists( 'NTH_TeamMembers' )  && class_exists( 'NTH_TeamMembers_Front' ) ) {
			$shortcodes['nth_teams'] = __CLASS__ . '::teammember';
		}
		
		if( self::checkPlugin( 'testimonials-by-woothemes/woothemes-testimonials.php' ) ) {
			$shortcodes['nth_testimonials'] = __CLASS__ . '::testimonials';
		}
		
		if( self::checkPlugin('features-by-woothemes/woothemes-features.php') ) {
			$shortcodes['nth_features'] = __CLASS__ . '::features';
		}
		
		if( self::checkPlugin( 'woocommerce/woocommerce.php' ) ) {
			$woo_args = parent::shortcodeArgs();
			$shortcodes = array_merge( $shortcodes, $woo_args );
		}
		
		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( $shortcode, $function );
		}
		
	}
	
	public static function banners( $atts, $content ){
		$atts = shortcode_atts(array(
			"link"			=> '#',
			"bg_image"		=> '',
			"css"			=> '',
			"hidden_on"		=> '',
			"class"			=> ''
		),$atts);
		
		$atts['content'] = strlen( trim( $content ) ) > 0 ? $content : '';
		 
		$classes = array('nth-shortcode', 'nth-banner');
		if( strlen($atts['class']) > 0 ) $classes[] = $atts['class'];
		
		if( strlen(trim($atts['hidden_on'])) > 0 ) $classes[] = $atts['hidden_on'];
		$atts['class'] = implode(' ', $classes);
		
		ob_start();
		
		parent::get_template("shortcode-banners.tpl.php", $atts);
		
		return ob_get_clean();
	}
	
	public static function brands( $atts ){
		$atts = shortcode_atts( array(
			'title'		=> '',
			'imgs' 		=> '',
			'column'	=> 6,
			'css'		=> ''
		), $atts );
		
		if( strlen( trim( $atts['imgs'] ) ) == 0 ) return;
		
		ob_start();
		
		parent::get_template("shortcode-brands.tpl.php", $atts);
		
		return '<div class="nth-shortcode nth-'. __FUNCTION__ .'">' . ob_get_clean() . '</div>';
	}
	
	public static function feedburner( $atts ){
		$atts = shortcode_atts( array(
			'fb_id' => 'kinhdon/Ahzl',
		), $atts );
		
		ob_start();
		
		parent::get_template("shortcode-feedburner.tpl.php", $atts);
		
		return '<div class="nth-shortcode '. __FUNCTION__ .'">' . ob_get_clean() . '</div>';
	}
	
	public static function infobox( $atts ){
		
		$atts = shortcode_atts(array(
			"title" 					=> 'title',
			"desc"						=> '',
			"use_icon"					=> 'yes',
			"type"						=> 'fontawesome',
			"icon_fontawesome"			=> 'fa fa-adjust',
			"icon_openiconic"			=> 'vc-oi vc-oi-dial',
			"icon_typicons"				=> 'typcn typcn-adjust-brightness',
			"icon_entypo"				=> 'entypo-icon entypo-icon-note',
			"icon_linecons"				=> 'vc_li vc_li-heart',
			"background_style"			=> 'rounded',
			"color"						=> 'black',
			"custom_color"				=> 'inherit',
			"icon_background"			=> 'white',
			"custom_icon_background" 	=> '#ededed',
		),$atts);
		
		ob_start();
		
		parent::get_template("shortcode-infobox.tpl.php", $atts);
		
		return '<div class="nth-shortcode nth-'. __FUNCTION__ .'">' . ob_get_clean() . '</div>';
	}
    
    public static function recent_comments( $atts ){
        $atts = shortcode_atts(array(
			"title" 		=> '',
			"limit"			=> 5,
			"as_widget"		=> 0,
		),$atts);
		
		$comments = get_comments( apply_filters( 'widget_comments_args', array(
			'number'      => $atts['limit'],
			'status'      => 'approve',
			'post_status' => 'publish'
		) ) );
		
		ob_start();
		
		if( $comments ) {
			
			$atts['comments'] = $comments;
			
			parent::get_template("shortcode-comments.tpl.php", $atts);
			
		}
		
		return '<div class="nth-shortcode recent-comments">' . ob_get_clean() . '</div>';
    }
	
	public static function recent_posts( $atts ) {
		$atts = shortcode_atts(array(
			"title" 		=> '',
			"cats"			=> '',
			"limit"			=> 5,
			"excerpt_words"	=> 15,
			"as_widget"		=> 0,
			"style"			=> ''
		),$atts);
		
		$args = array(
			'post_type' 			=> 'post'
			,'ignore_sticky_posts' 	=> 1
			,'showposts' 			=> $atts['limit']
		);
		
		if( strlen( $atts['cats'] ) > 0 ){
			$args['category__in '] = explode( ',', $atts['cats'] );
		}
		
		ob_start();
		
		$_post = new WP_Query( $args );
		
		if( $_post->have_posts() ) {
			
			$atts['_post'] = $_post;
			
			parent::get_template("shortcode-post-widget.tpl.php", $atts);
			
		}
		
		wp_reset_postdata();
		
		return '<div class="nth-shortcode recent-post">' . ob_get_clean() . '</div>';
	}
	
	public static function staticblock( $atts ) {
		if( !class_exists( 'NTH_StaticBlock' ) ) return '';
		extract(shortcode_atts(array(
			"title" 		=> '',
			"id"			=> '',
			"style"			=> '',
		),$atts));
		
		ob_start();
		
		if( strcmp($style, 'grid') == 0 ) {
			NTH_StaticBlock::getImage( $id );
		}
		
		echo '<div class="shortcode-content">';
		
		NTH_StaticBlock::getSticBlockContent( $id );
		
		echo '</div>';
		
		$class = ( strcmp($style, 'grid') == 0 )? 'widget_boxed': '';
		
		return '<div class="nth-shortcode '.esc_attr($class).' nth-'. __FUNCTION__ .'">' . ob_get_clean() . '</div>';
	}
	
	public static function testimonials( $atts ){
		$atts = shortcode_atts(array(
			"title" => '',
			"ids"	=> ''
		),$atts);
		
		if( strlen( trim( $atts['ids'] ) ) == 0 ) return '';
		
		$nth_testimonials = woothemes_get_testimonials( array('id' => $atts['ids'],'limit' => 10, 'size' => 'thumbnail' ));
		
		ob_start();
		
		if( !empty( $nth_testimonials ) && count( $nth_testimonials ) > 0 ) {
			
			$atts['nth_testimonials'] = $nth_testimonials;
			
			parent::get_template("shortcode-testimonials.tpl.php", $atts);
			
		}
		
		//rewind_posts();
		
		//wp_reset_query();
		wp_reset_postdata();
		
		return '<div class="nth-shortcode nth_'. __FUNCTION__ .'">' . ob_get_clean() . '</div>';
	}
	
	public static function features( $atts ){
		$atts = shortcode_atts(array(
			"title" 	=> '',
			"id"		=> 0,
			"per_row"	=> 3,
			"limit"		=> 5,
			"size"		=> 150,
			"color"		=> '',
			"t_color"	=> '',
			"l_color"	=> '',
			"style"		=> '',
			"w_limit"	=> -1,
			"use_boxed"	=> '0',
			"learn_more" => '0'
		),$atts);
		
		if( strlen( trim( $atts['id'] ) ) == 0 ) return '';
		
		$nth_features = woothemes_get_features( array('id' => $atts['id'],'limit' => $atts['limit'], 'size' => $atts['size']));
		
		ob_start();
		
		if( !empty( $nth_features ) && count( $nth_features ) > 0 ) {
			
			$atts['nth_features'] = $nth_features;
			
			parent::get_template("shortcode-features.tpl.php", $atts);
			
		}
		
		wp_reset_postdata();
		
		return '<div class="widget widget_woothemes_features">'.ob_get_clean().'</div>';
	}
	
	public static function teammember( $atts ){
		$atts = shortcode_atts(array(
			"title" 	=> '',
			"ids"		=> '',
			"columns"	=> 4,
			"style"		=> '',
		),$atts);
		
		if( strlen( trim( $atts['ids'] ) ) == 0 ) return '';
		$ids = array_map( 'trim', explode( ',', $atts['ids'] ) );
		
		if( !class_exists( 'NTH_TeamMembers_Front' ) ) return;
		
		$front = new NTH_TeamMembers_Front;
		$teams = $front->getByIds( $ids );
		
		ob_start();
		
		if( $teams->have_posts() ) {
			
			$atts['teams'] = $teams;
			
			parent::get_template("team-members.tpl.php", $atts);
			
		}
		
		wp_reset_postdata();
		
		return ob_get_clean();
	}
	
	public static function pricing( $atts, $content ){
		$atts = shortcode_atts(array(
			"title" 	=> 'Basic',
			"price"		=> '$|10.99|mo',
			"desc"		=> '',
			"features"	=> '',
			"label"		=> '',
			"label_text"	=> 'Most Popular',
			"bt_text"	=> 'Buy now',
			"bt_link"	=> '#',
		),$atts);
		
		$atts['content'] = $content;
		
		ob_start();
		
		parent::get_template("pricing.tpl.php", $atts);
		
		return '<div class="nth-shortcode nth-'. __FUNCTION__ .'">' . ob_get_clean() . '</div>';
	}
	
	public static function action( $atts ){
		extract(shortcode_atts( array(
			"label"		=> 'Message here...',
			"bt_text"	=> 'Button text',
			"bt_link"	=> '#',
			"bt_icon"	=> '',
			"use_icon"	=> 0,
			"bg_color"	=> '#ededed',
			"bt_color"	=> '#5a9e74',
		), $atts));
		
		if( absint($use_icon) ){
			$icon = strlen( $bt_icon ) > 0? '<i class="'.esc_attr($bt_icon).'"></i>': '';
		}
		
		$bt_link = self::convert_VC_link($bt_link);
		if( !isset($bt_link['title']) ) $bt_link['title'] = esc_attr($bt_text);
		
		$bt_link['target'] = isset($bt_link['target']) && strlen($bt_link['target']) > 0? 'target="'.esc_attr($bt_link['target']).'"' : '';
		
		if(!isset($bt_link['url']) || strlen($bt_link['url']) > 0) $bt_link['url'] = '#';
		
		ob_start(); ?>
		
		<span class="nth-label"><?php echo $label?></span>
		<a <?php echo $bt_link['target'];?> title="<?php echo esc_attr(urldecode($bt_link['title']));?>" href="<?php echo esc_url($bt_link['url']);?>"><?php echo esc_attr($bt_text);?> &nbsp;&nbsp;<?php echo $icon;?></a>
		<?php 
		return '<div class="nth-shortcode nth-'. __FUNCTION__ .'">' . ob_get_clean() . '</div>';
	}
	
	public static function google_maps( $atts, $content ){
		$atts = shortcode_atts(array(
			'title' 	=> '',
			'address'	=> 'Quan 1, Ho Chi Minh, Viet Nam',
			'zoom'		=> '16',
			'height'	=> '450px',
			'style'		=> '',
			'mk_icon'	=> '',
			'm_color'	=> 'JTVCJTBBJTIwJTIwJTIwJTIwJTdCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjAlMjJhZG1pbmlzdHJhdGl2ZS5jb3VudHJ5JTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZWxlbWVudFR5cGUlMjIlM0ElMjAlMjJsYWJlbHMlMjIlMkMlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJzdHlsZXJzJTIyJTNBJTIwJTVCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTdCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIydmlzaWJpbGl0eSUyMiUzQSUyMCUyMm9mZiUyMiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU3RCUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU1RCUwQSUyMCUyMCUyMCUyMCU3RCUyQyUwQSUyMCUyMCUyMCUyMCU3QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMmZlYXR1cmVUeXBlJTIyJTNBJTIwJTIybGFuZHNjYXBlLm5hdHVyYWwubGFuZGNvdmVyJTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZWxlbWVudFR5cGUlMjIlM0ElMjAlMjJhbGwlMjIlMkMlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJzdHlsZXJzJTIyJTNBJTIwJTVCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTdCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyY29sb3IlMjIlM0ElMjAlMjIlMjNlYmU3ZDMlMjIlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlN0QlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlNUQlMEElMjAlMjAlMjAlMjAlN0QlMkMlMEElMjAlMjAlMjAlMjAlN0IlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJmZWF0dXJlVHlwZSUyMiUzQSUyMCUyMmxhbmRzY2FwZS5tYW5fbWFkZSUyMiUyQyUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMmVsZW1lbnRUeXBlJTIyJTNBJTIwJTIyYWxsJTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyc3R5bGVycyUyMiUzQSUyMCU1QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU3QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMnZpc2liaWxpdHklMjIlM0ElMjAlMjJvZmYlMjIlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlN0QlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlNUQlMEElMjAlMjAlMjAlMjAlN0QlMkMlMEElMjAlMjAlMjAlMjAlN0IlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJmZWF0dXJlVHlwZSUyMiUzQSUyMCUyMndhdGVyJTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZWxlbWVudFR5cGUlMjIlM0ElMjAlMjJnZW9tZXRyeS5maWxsJTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyc3R5bGVycyUyMiUzQSUyMCU1QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU3QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMmNvbG9yJTIyJTNBJTIwJTIyJTIzODY5M2EzJTIyJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTdEJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTVEJTBBJTIwJTIwJTIwJTIwJTdEJTJDJTBBJTIwJTIwJTIwJTIwJTdCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjAlMjJyb2FkLmFydGVyaWFsJTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZWxlbWVudFR5cGUlMjIlM0ElMjAlMjJhbGwlMjIlMkMlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJzdHlsZXJzJTIyJTNBJTIwJTVCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTdCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIydmlzaWJpbGl0eSUyMiUzQSUyMCUyMm9mZiUyMiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU3RCUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU1RCUwQSUyMCUyMCUyMCUyMCU3RCUyQyUwQSUyMCUyMCUyMCUyMCU3QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMmZlYXR1cmVUeXBlJTIyJTNBJTIwJTIycm9hZC5sb2NhbCUyMiUyQyUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMmVsZW1lbnRUeXBlJTIyJTNBJTIwJTIyYWxsJTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyc3R5bGVycyUyMiUzQSUyMCU1QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU3QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMnZpc2liaWxpdHklMjIlM0ElMjAlMjJvZmYlMjIlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlN0QlMkMlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlN0IlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJjb2xvciUyMiUzQSUyMCUyMiUyM2ViZTdkMyUyMiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU3RCUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU1RCUwQSUyMCUyMCUyMCUyMCU3RCUyQyUwQSUyMCUyMCUyMCUyMCU3QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMmZlYXR1cmVUeXBlJTIyJTNBJTIwJTIyYWRtaW5pc3RyYXRpdmUubmVpZ2hib3Job29kJTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZWxlbWVudFR5cGUlMjIlM0ElMjAlMjJhbGwlMjIlMkMlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJzdHlsZXJzJTIyJTNBJTIwJTVCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTdCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIydmlzaWJpbGl0eSUyMiUzQSUyMCUyMm9uJTIyJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTdEJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTVEJTBBJTIwJTIwJTIwJTIwJTdEJTJDJTBBJTIwJTIwJTIwJTIwJTdCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjAlMjJwb2klMjIlMkMlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJlbGVtZW50VHlwZSUyMiUzQSUyMCUyMmFsbCUyMiUyQyUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMnN0eWxlcnMlMjIlM0ElMjAlNUIlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlN0IlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJ2aXNpYmlsaXR5JTIyJTNBJTIwJTIyb2ZmJTIyJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTdEJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTVEJTBBJTIwJTIwJTIwJTIwJTdEJTJDJTBBJTIwJTIwJTIwJTIwJTdCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjAlMjJ0cmFuc2l0JTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyZWxlbWVudFR5cGUlMjIlM0ElMjAlMjJhbGwlMjIlMkMlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjJzdHlsZXJzJTIyJTNBJTIwJTVCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTdCJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIydmlzaWJpbGl0eSUyMiUzQSUyMCUyMm9mZiUyMiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU3RCUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU1RCUwQSUyMCUyMCUyMCUyMCU3RCUyQyUwQSUyMCUyMCUyMCUyMCU3QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMmZlYXR1cmVUeXBlJTIyJTNBJTIwJTIycm9hZCUyMiUyQyUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMmVsZW1lbnRUeXBlJTIyJTNBJTIwJTIyYWxsJTIyJTJDJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIyc3R5bGVycyUyMiUzQSUyMCU1QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCU3QiUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMnZpc2liaWxpdHklMjIlM0ElMjAlMjJvZmYlMjIlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlN0QlMEElMjAlMjAlMjAlMjAlMjAlMjAlMjAlMjAlNUQlMEElMjAlMjAlMjAlMjAlN0QlMEElNUQ='
		),$atts);
		
		ob_start();
		
		$atts['map_id'] = rand();
		$atts['content'] = $content;
		
		parent::get_template("google-maps.tpl.php", $atts);
		
		return '<div class="nth-shortcode nth-'. __FUNCTION__ .'">' . ob_get_clean() . '</div>';
	}
	
	public static function convert_VC_link( $link_str ){
		if( strlen( trim($link_str) ) == 0 ) return '#';
		$link_rgs = array_map( 'trim', explode( '|', $link_str ) );
		$return = array();
		foreach($link_rgs as $var){
			$vars = array_map('trim', explode(':', $var));
			if( isset( $vars[1] ) && strlen(trim($vars[1])) > 0 ) $return[$vars[0]] = $vars[1];
		}
		return $return;
	}
	
}

endif;