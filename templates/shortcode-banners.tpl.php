
<div class="<?php echo esc_attr( $class );?>">
	<figure>
		<?php if( strlen( $bg_image ) > 0 ):?>
		<?php $bg_img_url = is_numeric($bg_image)? wp_get_attachment_url( $bg_image ) : $bg_image;?>
		<img src="<?php echo esc_url($bg_img_url);?>" alt="banner background image" />
		<?php endif;?>
		
		<?php if( strlen( trim($content) ) > 0 ):?>
		<figcaption>
			<?php echo do_shortcode( $content );?>
		</figcaption>
		<?php endif;?>
	</figure>
</div>

<?php if( strlen( trim( $css ) ) > 0 ):?>
<style type="text/css" data-type="vc_shortcodes-custom-css">
	<?php echo $css;?>
</style>
<?php endif;?>