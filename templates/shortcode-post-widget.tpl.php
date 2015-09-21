
<?php if( strlen( $title ) > 0 ):?>

	<h3 class="heading-title"><?php echo esc_html($title);?></h3>
	
<?php endif;?>

<ul class="<?php echo absint($as_widget)? 'list-post-widget' : 'list-posts row';?>">
	
	<?php while( $_post->have_posts() ): $_post->the_post(); global $post; ?>
	
		<?php set_query_var( 'excerpt_words', $excerpt_words );?>
	
		<?php set_query_var( 'shortcode_style', $style );?>
	
		<?php if(absint($as_widget)):?>
	
			<?php get_template_part( 'content', 'widget' ); ?>
	
		<?php else: ?>
		
			<?php get_template_part( 'content', get_post_format() ); ?>
	
		<?php endif;?>

	<?php endwhile; ?>
	
</ul>
