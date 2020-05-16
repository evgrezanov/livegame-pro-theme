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

// link featured images
function change_custom_post_type_process() {
	register_batch_process( array(
		'name' => 'Change features image for teams',
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

// new columns 
function get_all_sports_leagues() {
	register_batch_process( array(
		'name' => 'Add team id columns to custom tables',
		'type' => 'term',
		'args' => array(
            'taxonomy' => 'sports',
            'exclude'   => array(2,4),
		),
		'callback' => 'lgp_add_new_columns',
	) );
}
add_action( 'locomotive_init', 'get_all_sports_leagues' );

function lgp_add_new_columns( $category ) {
    global $wpdb;
    $query = "ALTER TABLE " . $wpdb->prefix . $category->slug . " ADD team1_id int(10)";
    $res = $wpdb->query($query);
    
    $query2 = "ALTER TABLE " . $wpdb->prefix . $category->slug . " ADD team2_id int(10)";
	$res2 = $wpdb->query($query2);

}

// link teams
function add_team_id_to_custom_table() {
	register_batch_process( array(
		'name' => 'Link teams to custom tables',
		'type' => 'post',
		'args' => array(
            'post_type' => 'teams',
            'posts_per_page' => -1,
		),
		'callback' => 'process_add_team_id',
	) );
}
add_action( 'locomotive_init', 'add_team_id_to_custom_table' );

function process_add_team_id( $post ) {
    global $wpdb;
    $cur_terms = get_the_terms( $post, 'sports' );
    if( is_array( $cur_terms ) ){
        foreach( $cur_terms as $cur_term ){
            if ($cur_term->slug != 'hokkey' && $cur_term->slug != 'soccer'):
                $t1res = $wpdb->update( 
                    $wpdb->prefix.$cur_term->slug,
	                array( 'team1_id' => $post->ID),
                    array( 'team1' => $post->post_title ),
                    array( '%d' ),
                    array( '%s' )
                );
                $t2res = $wpdb->update( 
                    $wpdb->prefix.$cur_term->slug,
	                array( 'team2_id' => $post->ID),
                    array( 'team2' => $post->post_title ),
                    array( '%d' ),
                    array( '%s' )
                );
            endif;
        }
    }
}