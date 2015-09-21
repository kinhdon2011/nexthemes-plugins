<?php if( strlen( trim( $title ) ) > 0 ):?>

	<h3 class="heading-title"><?php echo esc_html( $title );?></h3>

<?php endif;?>

<?php foreach( $nth_testimonials as $testimonial ):?>
				
	<?php $post = $testimonial;?>
	
	<?php setup_postdata( $post );?>
	
	<div class="testimonials-item">
	
		<div class="description"><?php echo get_the_content();?></div>
		
		<h3><?php echo $post->post_title;?></h3>
	
	</div>

<?php endforeach;?>