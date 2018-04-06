<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
/**
 * This file handles the faculty search results page.
*/
/*********Make it sidebar content layout.**************/
add_filter('genesis_pre_get_option_site_layout', '__genesis_return_sidebar_content');
/*********Don't display the post meta after each post.**************/
remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
/*********Add the search class to the page body for optional theme styling**************/
function uvasomrfdsearch_add_classes( $classes ) {
	$classes[] = 'search';
	return $classes;
}
add_filter( 'body_class', 'uvasomrfdsearch_add_classes' );
/**************************************************************************************************/
//THESE LAYOUT ADJUSTMENTS ARE  SPECIFIC TO THE UVASOM BIMS THEME ONLY//////////////////////////////
/**************************************************************************************************/
/*********Move the page title from its default location, per the BIMS Theme**************/
if (get_stylesheet() =='uvasom_bims') {
add_action( 'genesis_post_title','genesis_do_post_title' );
add_action( 'genesis_after_header', 'uvasomrfd_do_search_title' );
}
if (get_stylesheet() =='uvasom_news') {
add_action( 'genesis_post_title','genesis_do_post_title' );
add_action( 'genesis_before_loop', 'uvasomrfd_do_search_title' );
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
}
/****declare variables needed for layout******/
	$taxonomy = 'primary'; //change me
    $term = get_query_var( 'term' );
    $term_obj = get_term_by( 'slug' , $term , $taxonomy );
/*********Get rid of the home page layout stuff if this is the UVASOM News Theme**************/
/**************************************************************************************************/
function uvasomrfd_do_search_title() {
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	if (is_tax( 'primary')||is_tax( 'training-grant')) {$preterm='Department of ';}
	if (is_tax( 'research-discipline')) {$preterm='Research Area: ';}
	$title = sprintf( '<div class="clearfix"></div><div id="uvasom_page_title">'.genesis_do_breadcrumbs().'<h1 class="archive-title">%s %s</h1>', apply_filters( 'genesis_search_title_text', __( $preterm, 'genesis' ) ), $term->name).'</div>';
	echo apply_filters( 'genesis_search_title_output', $title ) . "\n";
}
/*********Remove the default archive listing **************/
remove_action( 'genesis_loop', 'genesis_do_loop' );
/*********include the custom faculty listing archive listing **************/
add_action('genesis_loop','uvasomrfd_do_loop');
/*********function defining display custom faculty listing archive listing **************/
function uvasomrfd_do_loop() {
global $wpdb;
global $post;
$wpdb->show_errors(); 
$traininggrantparticipants = $wpdb->get_var("SELECT post_title FROM $wpdb->posts INNER JOIN $wpdb->postmeta ON post_id LIKE meta_id  WHERE $wpdb->posts.post_type LIKE 'faculty-listing' AND wpdb->postmeta.training-grant LIKE 'Pharma' ORDER BY $wpdb->postmeta.wpcf-last-name DESC");
$pageposts = $wpdb->get_results($traininggrantparticipants, OBJECT);
echo $pageposts;}