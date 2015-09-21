<?php 

/**
 * Plugin Name: Nexthemes Plugins
 * Plugin URI: http://nexthemes.com/
 * Description: Nexthemes Plugins. It support only to Nexthemes.
 * Version: 1.0
 * Author: Nexthemes
 * Author URI: http://nexthemes.com/
 *
 * License: GPLv2 or later
 * Text Domain: nexthemes-plugin
 * Domain Path: /languages/
 *
 * @package Nexthemes Plugins
 * @author Nexthemes
 */


if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'NexThemes_Plg' ) ):

final class NexThemes_Plg {
	
	public $version = '1.0';
	
	public function __construct(){
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
		
	}
	
	private function define_constants(){
		$this->define( 'NTH_PLUGIN_FILE', __FILE__ );
		$this->define( 'NTH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'NTH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		$this->define( 'NTH_PLUGIN_TMPL_DIR', NTH_PLUGIN_DIR . 'templates/' );
		$this->define( 'NTH_VERSION', $this->version );
	}
	
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
	
	public function load_plugin_textdomain(){
		$locale = get_locale();
		
		load_textdomain( 'nexthemes-plugin', WP_LANG_DIR . '/nexthemes-plugin/nexthemes-plugin-' . $locale . '.mo' );
		load_plugin_textdomain( 'nexthemes-plugin', false, plugin_basename( dirname( __FILE__ ) ) . "/languages" );
	}
	
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
	
	public function includes(){
		
		include_once( 'includes/class-nth-install.php' );
		
		include_once( 'includes/class.settings-api.php' );
		include_once( 'includes/class-nth-plugin-panel.php' );
		include_once( 'includes/class-nth-admin-notices.php' );
		
		include_once( 'includes/class-nth-frontend-scripts.php' );
		
		$features = get_option('nexthemes_pl_settings');
		
		$features = isset($features['dis_features'])? $features['dis_features'] : array();
		
		if( !in_array( 'staticblock', $features ) )
			$this->include_staticblocks();
		
		if( !in_array( 'portfolio', $features ) )
			$this->include_portfolios();
		
		if( !in_array( 'teams', $features ) )
			$this->include_members();
		
		$this->include_shortcodes();
		
		if( self::checkPlugin( 'woocommerce/woocommerce.php' ) ) {
			if( !in_array( 'gridlisttoggle', $features ) )
				include_once( 'includes/woo-gridlisttoggle/class-gridlisttoggle.php' );
		}
	}
	
	public function include_portfolios(){
		include_once( 'includes/portfolios/class-portfolios.php' );
		if( $this->is_request( 'admin' ) ) {
			include_once( 'includes/portfolios/class-portfolios-admin.php' );
		} else {
			include_once( 'includes/portfolios/class-portfolios-front.php' );
		}
	}
	
	public function include_staticblocks(){
		include_once( 'includes/staticblocks/class-staticblocks.php' );
	}
	
	public function include_members(){
		include_once( 'includes/teams/class-team-members.php' );
		if( $this->is_request( 'admin' ) ) {
			include_once( 'includes/teams/class-team-members-admin.php' );
		} else {
			include_once( 'includes/teams/class-team-members-front.php' );
		}
	}
	
	public function include_shortcodes(){
		if( $this->is_request( 'frontend' ) ) {
			include_once( 'includes/shortcodes/class-woo-shortcodes.php' );
			include_once( 'includes/shortcodes/class-shortcodes.php' );
		}
	}
	
	public function init_hooks(){
		register_activation_hook( __FILE__, array( 'NTH_Installations', 'install' ) );
		$__plugin_panel = new NTH_Plugin_Panel();
		add_action( 'init', array( $this, 'init' ), 0 );
		
		if( $this->is_request( 'frontend' ) ) {
			add_action( 'init', array( 'NTH_Shortcodes', 'init' ) );
		}
		
	}
	
	public function init(){
		// Set up localisation
		//add_action( 'plugins_loaded', array($this, 'load_plugin_textdomain') );
		$this->load_plugin_textdomain();
	}
	
	public static function checkPlugin( $path = '' ){
		if( strlen( $path ) == 0 ) return false;
		$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
		if ( in_array( trim( $path ), $_actived ) ) return true;
		else return false;
	}
	
}

$nexthemes_plg = new NexThemes_Plg();

endif;

