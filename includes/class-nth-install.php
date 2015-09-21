<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

class NTH_Installations {
	
	public function __construct(){
		
	}
	
	public static function install(){
		flush_rewrite_rules();
		
	}
	
}