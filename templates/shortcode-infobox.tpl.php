<?php 
// Enqueue needed icon font.
vc_icon_element_fonts_enqueue( $type );
$iconClass = array();
$iconClass[] = isset( ${"icon_" . $type} ) ? esc_attr( ${"icon_" . $type} ) : 'fa fa-adjust';
$iconClass[] = $background_style;

$color = strcmp( $color, 'custom' ) == 0? $custom_color: $color;
$icon_bg = strcmp( $icon_background, 'custom' ) == 0? $custom_icon_background: $icon_background;

?>

<span style="color: <?php echo esc_attr($color);?>; background-color: <?php echo esc_attr($icon_bg);?>" class="nth-icon <?php echo esc_attr( implode( ' ', $iconClass ) );?>"></span>
<h3 class="infobox-title" style="color: <?php echo esc_attr($color);?>;"><?php echo esc_attr( $title );?></h3>
<?php if( strlen( $desc ) > 0 ): ?><p><?php echo esc_attr( $desc );?></p><?php endif;?>