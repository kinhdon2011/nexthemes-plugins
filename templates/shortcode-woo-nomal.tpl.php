<?php 
	global $woocommerce_loop;
	
	if( absint($as_widget) ) {
		if( strcmp( $item_style, 'grid' ) == 0 ) {
			$woocommerce_loop['columns'] = 2;
		} else {
			$woocommerce_loop['columns'] = 1;
		}
	} else $woocommerce_loop['columns'] = $columns;
?>

<?php if( !absint($as_widget) ): ?>
	
	<?php 
	if( isset( $is_slider ) && absint( $is_slider ) ) {
		$options = array(
			"items"			=> $columns,
			"itemsDesktop"		=> [1199,round( $columns * (1199 / 1200) )],
			"itemsDesktopSmall"	=> [980, round( $columns * (980  / 1200) )],
			"itemsTablet"		=> [768, round( $columns * (768 / 1200) )],
			"itemsMobile"		=> [479, round( $columns * (479 / 1200) )]
		);
		
		printf('<div class="nth-woo-shortcode nth-owlCarousel loading" data-options="%1$s" data-slider="%2$s" data-base="%3$d">', esc_attr(json_encode($options)), '.products', 1);
	} else {
		echo '<div class="nth-woo-shortcode">';
	}
	?>
	
		<div class="nth-shortcode-header">
				
			<?php if( strlen( $title ) > 0 ):?>
			
			<h3 class="heading-title"><?php echo esc_attr($title);?></h3>
			
			<?php endif;?>
			
		</div>
	
		<div class="row">	
			
			<?php if(isset($excerpt_limit)): ?>
				<?php add_filter( 'nth_woocommerce_short_description_count', function($limit) use( $excerpt_limit ) { return $excerpt_limit;}, 10, 1 );?>
			<?php endif;?>
			
			<?php wc_get_template( 'loop/loop-start.php', array("item_style" => $item_style) ); ?>
			
				<?php while ( $products->have_posts() ) : $products->the_post(); ?>
				
					<?php wc_get_template_part( 'content', 'product' ); ?>
				
				<?php endwhile; // end of the loop. ?>
			
			<?php woocommerce_product_loop_end(); ?>
			
			<?php if(isset($excerpt_limit)): ?>
				<?php remove_filter( 'nth_woocommerce_short_description_count', function($limit) use( $excerpt_limit ) { return $excerpt_limit;}, 10, 1 );?>
			<?php endif;?>
			
		</div><!-- END .nth-woo-shortcode -->
	
	<?php echo "</div>";?>

<?php else: ?>
	
	<?php if( strlen( $title ) > 0 ):?>
	
	<div class="widget-heading"><h3 class="widget-title heading-title"><?php echo esc_html($title);?></h3></div>
	
	<?php endif?>
	
	<div class="content-inner">
	
		<?php echo apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget '.esc_attr($item_style).'">' );?>
	
		<?php while ( $products->have_posts() ) : $products->the_post(); ?>
	
		<?php wc_get_template( 'content-widget-product.php', array( 'show_rating' => true ) );?>
	
		<?php endwhile;?>
	
		<?php echo apply_filters( 'woocommerce_after_widget_product_list', '</ul>' );?>
		
	</div>

<?php endif;?>