<?php 

?>

<div class="nth-pricing-wrapper">
	<ul class="nth-pricing-ul<?php if( strlen($label) > 0 ) echo ' '.esc_attr($label);?>">
		<?php if( strlen($label) > 0 ):?>
		<li class="pricing-label"><?php echo esc_attr($label_text);?></li>
		<?php endif;?>
		<li class="widget-heading">
			<h3 class="heading-title"><?php echo esc_attr( $title );?></h3>
		</li>
		<li class="prices">
			<span class="price_table">
				<?php 
				$prices = array_map('trim', explode('|', $price));
				if(count($prices) == 2) array_unshift( $prices, "" );
				?>
				<sup class="currency_symbol"><?php echo esc_attr( $prices[0] );?></sup>
				<span class="pricing"><?php echo esc_attr( $prices[1] );?></span>
				<sub class="mark"><?php printf( __('/%s', 'theshopier'), esc_attr( $prices[2] ) );?></sub>
			</span>
		</li>
		<?php if( strlen($desc) > 0 ):?>
		<li class="desc"><?php echo esc_attr($desc);?></li>
		<?php endif;?>
		
		<?php 
		if( strlen($features) > 0 ): 
			$features = (array) vc_param_group_parse_atts( $features );
			//$features = array_map( 'trim', explode(',', wp_strip_all_tags($features)) );
		?>
			<?php 
			foreach( $features as $feature ): 
				if( isset($feature['title']) && strlen($feature['title']) > 0 ):
			?>
			<li class="feature"><?php 
				echo do_shortcode($feature['title']);
				if( isset($feature['tooltip']) && strlen($feature['tooltip']) > 0 ) {
					echo '<span class="nth-more-info" data-toggle="tooltip" data-placement="top" title="'.esc_attr($feature['tooltip']).'">?</span>';
				}
			?></li>
			<?php 
				endif;
			endforeach;
			?>
		<?php endif;?>
		
		<li class="price-buttons"><a class="btn btn-primary" href="<?php echo esc_url($bt_link);?>" title="<?php echo esc_attr($bt_text);?>"><?php echo esc_attr($bt_text);?></a></li>
	</ul>
</div>