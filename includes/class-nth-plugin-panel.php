<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'NTH_Plugin_Panel' ) ) : 

class NTH_Plugin_Panel {
	
	public $settings = array();
	
	private $settings_api;
	
	public function __construct(){
		$this->settings_api = new WeDevs_Settings_API();
		
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		//add_action( 'admin_menu', array( $this, 'add_setting_page' ) );
		$this->notices();
	}
	
	public function notices(){
		$options = self::get_option();
		if( isset( $options['dis_features'] ) && is_array( $options['dis_features'] ) ) {
			if( in_array( 'staticblock', $options['dis_features'] ) ) {
				NTH_Admin_Notices::create( __('<strong>WARNING!</strong> If you disabled "Static block", some featured on theme will not working (Ex: Mega menu, Static block widget, Static block shortcode, ... )', 'nexthemes-plugin'), 'warning');
			}
		}
	}
	
	public function settings_init(){
		$this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );
		
		$this->settings_api->admin_init();
	}
	
	public function get_settings_sections() {
        $sections = array(
			array(
				'id' 	=> 'nexthemes_pl_settings',
				'class'	=> '',
				'title' => '<span class="dashicons-before dashicons-admin-settings"></span>' . __( 'Settings', 'nexthemes-plugin' )
			),
        );
        return $sections;
    }
	
	public function get_settings_fields() {
        $settings_fields = array(
			'nexthemes_pl_settings' => array(
				array(
					'name'    => 'dis_features',
					'label'   => __( 'Disable Features', 'nexthemes-plugin' ),
					'desc'    => __( 'Tick the features, that you want to disable', 'nexthemes-plugin' ),
					'type'    => 'multicheck',
					'options' => array(
						'staticblock'   	=> 'Static Block',
						'gridlisttoggle' 	=> 'Woo Grid List Toggle',
						'portfolio' 		=> 'Portfolio',
						'teams' 			=> 'Team Members',
					)
				),
			),
        );
		$options = self::get_option();
		if( count($options) == 0 || !isset($options['dis_features']) || !in_array( 'portfolio', $options['dis_features'] ) ) {
			$settings_fields['nexthemes_pl_settings'][] = array(
				'name'              => 'portfolio_thumb',
				'label'             => __( 'Portfolio thumb size', 'nexthemes-plugin' ),
				'type'              => 'image_size',
			);
		}
		
		if( count($options) == 0 || !isset($options['dis_features']) || !in_array( 'teams', $options['dis_features'] ) ) {
			$settings_fields['nexthemes_pl_settings'][] = array(
				'name'              => 'teams_thumb',
				'label'             => __( 'Team member thumb size', 'nexthemes-plugin' ),
				'type'              => 'image_size',
			);
		}

        return $settings_fields;
    }
	
	public static function get_option(){
		$options = get_option('nexthemes_pl_settings');
		return $options? $options : array();
	}
	
	public function add_menu_page() {
		add_menu_page( __( 'Nexthemes', 'nexthemes-plugin' ), __( 'Nexthemes', 'nexthemes-plugin' ), 'manage_options', 'nth_plugin_panel', array( $this, 'setting_panel' ), NTH_PLUGIN_URL . 'assets/images/nexthemes-pl-icon.png', 85 );
	}
	
	public function add_setting_page(){
		add_submenu_page( 'nth_plugin_panel', __( 'Nexthemes Settings', 'nexthemes-plugin' ), __( 'Settings', 'nexthemes-plugin' ), 'manage_options', 'nth_setting_panel', array( $this, 'setting_panel' ) );
		
		$this->remove_duplicate_submenu_page();
	}
	
	public function remove_duplicate_submenu_page() {
		remove_submenu_page( 'nth_plugin_panel', 'nth_plugin_panel' );
	}
	
	public function setting_panel(){
		
		echo '<div class="wrap">';
		
		//$this->settings_api->show_navigation();
        $this->settings_api->show_forms();
		
        echo '</div>';
		
	}
	
}

endif;