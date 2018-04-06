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
	if (is_tax( 'research-discipline')) {$preterm='Research Discipline: ';}
	if (is_tax( 'training-grant')) {$preterm='Training Program: ';}
	if (strpos($_SERVER["REQUEST_URI"], '/acceptsundergrads/')) {$preterm='Faculty Accepting Undergraduate Students';$term->name='';}
	$title = sprintf( '<div class="clearfix"></div><div id="uvasom_page_title">'.genesis_do_breadcrumbs().'<h1 class="archive-title">%s %s</h1>', apply_filters( 'genesis_search_title_text', __( $preterm, 'genesis' ) ), $term->name).'</div>';
	echo apply_filters( 'genesis_search_title_output', $title ) . "\n";
}
/*********Remove the default archive listing **************/
remove_action( 'genesis_loop', 'genesis_do_loop' );
/*********include the custom faculty listing archive listing **************/
add_action('genesis_loop','uvasomrfd_do_loop');
/*********function defining display custom faculty listing archive listing **************/
function uvasomrfd_do_loop() {
	if (is_tax( 'primary')){
	$taxonomy = 'primary'; 
	}
	if (is_tax('training-grant')) {
	$taxonomy = 'training-grant'; 
	}
	if (is_tax('other-affs')) {
	$taxonomy = 'other-affs'; 
	}
	if (is_tax('research-discipline')) {
	$taxonomy = 'research-discipline'; 
	}
	if (is_tax('research-discipline') || is_tax('training-grant')|| is_tax('other-affs')|| is_tax('primary')) {
    $term = get_query_var( 'term' );
    $term_obj = get_term_by( 'slug' , $term , $taxonomy );
    $cpt = 'faculty-listing'; 
	$paged = (get_query_var('paged'));
	$fac_args = array(
		'post_type' => $cpt, 
		'meta_key'=>'wpcf-last-name',
		'orderby' => 'meta_value',
		'order' => 'ASC',
		'posts_per_page' => 10,
 		'paged' => $paged,
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => array( $term_obj->slug )
					
				)
			)		
		);
			$loop = new WP_Query( $fac_args );

	}
	if (strpos($_SERVER["REQUEST_URI"], '/acceptsundergrads/')) {
		$paged = (get_query_var('paged'));
		$fac_args = array(
			'post_type' => 'faculty-listing', 
			'meta_query' => array(
				'key'     => 'wpcf-will-take-undergrads',
				'value'   => 1,
				'compare' => '='
			),
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => 10,
			'paged' => $paged,
		);
		$loop = new WP_Query( $fac_args );
}
	//uvasomrfd_faculty_printlist();
	if( $loop->have_posts() ):
		while( $loop->have_posts() ): $loop->the_post(); 
		global $post;
		
		$faclisting = '<div class="facultylist">'."\n";
		$faclisting .= '<a href="'.post_permalink( $post->ID ).'">';
		if (has_post_thumbnail( $post->ID ) ) {
			$faclisting .= get_the_post_thumbnail($post->ID);
		} 
		else {
		  $faclisting .= '<img class="nofacimage" src="/sharedassets/images/blankavatar.jpg" alt="No Photo Available"/>';
		}
		$faclisting .= '</a>'."\n";
		$faclisting .= '<h2><a href="'.post_permalink( $post->ID ).'">'.get_the_title().'</a></h2>'."\n";
		$faclisting .= '<p>'.get_post_meta(get_the_ID(),'wpcf-research-interest-title',true ) . '<p>'."\n";
		$faclisting .= '</div>'."\n";

		echo $faclisting;
		
		endwhile;
		genesis_posts_nav();
	endif;
	wp_reset_query();
	}
	
