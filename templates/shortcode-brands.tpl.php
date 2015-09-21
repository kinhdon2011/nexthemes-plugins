<?php 

$imgs = explode( ',', $imgs );

if( strlen( $title ) > 0 ) {
	echo '<h3 class="heading-title">'. esc_html($title) .'</h3>';
}

$options = array( 
	"items"				=> $column,
	"itemsDesktop"		=> [1199,round( $column * (1199 / 1200) )],
	"itemsDesktopSmall"	=> [980, round( $column * (980  / 1200) )],
	"itemsTablet"		=> [768, round( $column * (768 / 1200) )],
	"itemsMobile"		=> [479, round( $column * (479 / 1200) )],
);

$class = array();
$class[] = 'col-sm-24';
$ul_class = vc_shortcode_custom_css_class( $css );
?>
<ul class="row nth-owlCarousel loading <?php echo esc_attr($ul_class);?>" data-options="<?php echo esc_attr(json_encode($options));?>" data-base="1">
	<?php foreach( $imgs as $img ): ?>
	<li class="<?php echo esc_attr( implode( ' ', $class ) );?>">
		<div class="item-inner"><?php echo wp_get_attachment_image( $img, 'full' );?></div>
	</li>
	<?php endforeach;?>
</ul>