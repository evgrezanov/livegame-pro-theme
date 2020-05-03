<?php

// Include php files
include get_theme_file_path('/includes/shortcodes.php');

// Enqueue needed scripts
function needed_styles_and_scripts_enqueue() {
    
    // Add-ons

    
    // Custom script
    wp_enqueue_script( 'wpbs-custom-script', get_stylesheet_directory_uri() . '/assets/javascript/script.js' , array( 'jquery' ) );

    // enqueue style
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );


}
add_action( 'wp_enqueue_scripts', 'needed_styles_and_scripts_enqueue' );

function cc_mime_types($mimes) {
$mimes['svg'] = 'image/svg+xml';
return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


add_filter( 'widget_text', 'do_shortcode' );

//Dynamic Year
function site_year(){
	ob_start();
	echo date( 'Y' );
	$output = ob_get_clean();
    return $output;
}
add_shortcode( 'site_year', 'site_year' );

//
// Your code goes below
//

function change_custom_post_type_process() {
	register_batch_process( array(
		'name' => 'Change Custom Post Type',
		'type' => 'post',
		'args' => array(
            'post_type' => 'teams',
            'posts_per_page' => -1,
		),
		'callback' => 'process_feature_img_change',
	) );
}
add_action( 'locomotive_init', 'change_custom_post_type_process' );

function process_feature_img_change( $post ) {
    $media = get_attached_media('image', $post);
    $media = array_shift( $media );
    $image_id = $media->ID;
    set_post_thumbnail( $post->ID, $image_id );
}