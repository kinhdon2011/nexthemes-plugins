<?php
/**
 * @package nth-portfolios
 */

if( !class_exists( 'NTH_TeamMembers_Front' ) ) {
	
	class NTH_TeamMembers_Front extends NTH_TeamMembers {
		
		function __construct(){
			parent::__construct();
			
		}
		
		public function getByIds( $ids = array() ){
			if( count($ids) == 0 ) return '';
			
			$team = new WP_Query( array( 'post_type' => $this->post_type, 'post__in' => $ids ) );
			
			return $team;
		}
		
	}
	
}
