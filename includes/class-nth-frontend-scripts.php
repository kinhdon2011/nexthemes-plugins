<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'NTH_Frontend_Scripts' ) ) {
	
	class NTH_Frontend_Scripts {
	
		public function __construct(){
			
		}
		
		public static function init(){
			add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
			
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_load_scripts' ) );
		}
		
		public static function load_scripts(){
			wp_enqueue_style( 'nexthemes-style', NTH_PLUGIN_URL . 'assets/css/style.css' );
			
			wp_register_script( 'isotope.min', NTH_PLUGIN_URL . 'assets/js/isotope.min.js', false, '2.2.0', true );
			wp_enqueue_script( 'isotope.min' );
			
			if( NexThemes_Plg::checkPlugin('woocommerce/woocommerce.php') && !wp_script_is('jquery.prettyPhoto', 'enqueued') && !wp_script_is('jquery.prettyPhoto.min', 'enqueued') ) {
				wp_register_style( 'prettyPhoto', NTH_PLUGIN_URL . 'assets/css/prettyPhoto.css', false, '3.1.6' );
				wp_enqueue_style( 'prettyPhoto' );
				
				wp_register_script( 'jquery.prettyPhoto', NTH_PLUGIN_URL . 'assets/js/jquery.prettyPhoto.js', false, '3.1.6', true );
				wp_enqueue_script( 'jquery.prettyPhoto' );
				
				wp_enqueue_script( 'cookie', NTH_PLUGIN_URL . 'assets/js/jquery.cookie.min.js', array( 'jquery' ) );
			}
			
			//wp_enqueue_style( 'dashicons' );
			
			wp_register_script( 'nextheme-plugin-js', NTH_PLUGIN_URL . 'assets/js/nexthemes.js', array('jquery'), NTH_VERSION, true );
			wp_enqueue_script( 'nextheme-plugin-js' );
			
		}
		
		public static function localize_printed_scripts(){
			$localizes = array(
				'ajax_url'	=> admin_url( 'admin-ajax.php' ),
				'nonce'		=> wp_create_nonce('nth_plugin_none_339419'),
				'data'		=> array(
					'maps'	=> array(
						'styles'	=> array(
							'FACEBOOK'	=> array(
								array(
									'featureType'	=> 'water',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('color' => "#3b5998")
									)
								),
								array(
									'featureType'	=> 'administrative.province',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('visibility' => "off")
									)
								),
								array(
									'featureType'	=> 'all',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('hue' => '#3b5998'),
										array('saturation' => -22)
									)
								),
								array(
									'featureType'	=> 'landscape',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('visibility' => 'on'),
										array('color' => '#f7f7f7'),
										array('saturation' => 10),
										array('lightness' => 76)
									)
								),
								array(
									'featureType'	=> 'landscape.natural',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('color' => '#f7f7f7')
									)
								),
								array(
									'featureType'	=> 'road.highway',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('color' => '#8b9dc3'),
										array('visibility' => 'simplified')
									)
								),
								array(
									'featureType'	=> 'road.highway',
									'elementType'	=> 'labels.icon',
									'stylers'		=> array(
										array('visibility' => 'off')
									)
								),
								array(
									'featureType'	=> 'road.local',
									'elementType'	=> 'geometry.fill',
									'stylers'		=> array(
										array('color' => '#8b9dc3')
									)
								),
								array(
									'featureType'	=> 'administrative.country',
									'elementType'	=> 'geometry.stroke',
									'stylers'		=> array(
										array('visibility' => 'simplified'),
										array('color' => '#3b5998')
									)
								),
								array(
									'featureType'	=> 'administrative',
									'elementType'	=> 'labels.icon',
									'stylers'		=> array(
										array('visibility' => 'on'),
										array('color' => '#3b5998')
									)
								),
								array(
									'featureType'	=> 'transit.line',
									'elementType'	=> 'geometry.stroke',
									'stylers'		=> array(
										array('invert_lightness' => 0),
										array('color' => '#ffffff'),
										array('weight' => 0.43)
									)
								)
							),
							'GRAY'		=> array(
								array(
									'featureType'	=> 'all',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('saturation' => -100)
									)
								),
								array(
									'featureType'	=> 'water',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('color' => '#6f6f6f')
									)
								)
							),
							'LIGHTGRAY'	=> array(
								array(
									'featureType'	=> 'landscape',
									'elementType'	=> 'geometry.fill',
									'stylers'		=> array(
										array('color' => '#edecea')
									)
								),
								array(
									'featureType'	=> 'landscape',
									'elementType'	=> 'geometry.stroke',
									'stylers'		=> array(
										array('color' => '#edecea')
									)
								),
								array(
									'featureType'	=> 'poi',
									'elementType'	=> 'geometry.stroke',
									'stylers'		=> array(
										array('visibility' => 'off')
									)
								),
								array(
									'featureType'	=> 'poi',
									'elementType'	=> 'geometry.fill',
									'stylers'		=> array(
										array('visibility' => 'on'),
										array('color' => '#f7f7f7')
									)
								),
								array(
									'featureType'	=> 'road',
									'elementType'	=> 'geometry.stroke',
									'stylers'		=> array(
										array('color' => '#e6e6e6')
									)
								),
								array(
									'featureType'	=> 'water',
									'elementType'	=> 'geometry.fill',
									'stylers'		=> array(
										array('color' => '#bfbfbf')
									)
								),
								array(
									'featureType'	=> 'road.highway',
									'elementType'	=> 'geometry.fill',
									'stylers'		=> array(
										array('color' => '#d9d9d9')
									)
								),
								array(
									'featureType'	=> 'road',
									'elementType'	=> 'labels.text.fill',
									'stylers'		=> array(
										array('color' => '#424242')
									)
								),
								array(
									'featureType'	=> 'road.local',
									'elementType'	=> 'geometry.fill',
									'stylers'		=> array(
										array('saturation' => -100)
									)
								),
								array(
									'featureType'	=> 'water',
									'elementType'	=> 'labels.text.fill',
									'stylers'		=> array(
										array('color' => '#ffffff')
									)
								),
								array(
									'featureType'	=> 'water',
									'elementType'	=> 'labels.text.stroke',
									'stylers'		=> array(
										array('color' => '#a6a6a6')
									)
								),
								array(
									'featureType'	=> 'poi.business',
									'elementType'	=> 'labels',
									'stylers'		=> array(
										array('saturation' => -100)
									)
								),
								array(
									'featureType'	=> 'poi',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('saturation' => -100)
									)
								),
								array(
									'featureType'	=> 'transit',
									'elementType'	=> 'all',
									'stylers'		=> array(
										array('saturation' => -100),
										array('lightness', 1)
									)
								)
							)
						)
					),
				),
			);
			
			wp_localize_script('nextheme-plugin-js', 'NexThemes', $localizes);
		}
		
		public static function admin_load_scripts(){
			wp_enqueue_style( 'nexthemes-adminstyle', NTH_PLUGIN_URL . 'assets/css/admin-style.css' );
		}
		
	}
	NTH_Frontend_Scripts::init();
}
