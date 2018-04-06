<?php
/*
Plugin Name: UVA Health/School of Medicine Research Faculty Directory (FOR MAIN RFD SITE ONLY)
Plugin URI: http://technology.med.virginia.edu/digitalcommunications
Description: Aggregates Curvita data into a searchable frontend public framework.
Version: 0.1
Author: Cathy Finn-Derecki
Author URI: http://transparentuniversity.com
Copyright 2012  Cathy Finn-Derecki  (email : cad3r@virginia.edu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//include widget
require_once(dirname( __FILE__ ). '/uvasomrfd_search_widget.php');
require_once(dirname( __FILE__ ). '/uvasomrfd_unleash_widget.php');
require_once(dirname( __FILE__ ). '/uvasomrfd_breadcrumbs.php');
//require_once(dirname( __FILE__ ). '/uvasomrfd_print_list.php');
//require_once(dirname( __FILE__ ). '/uvasomrfd_search_researchareas_widget.php');
/*********add css for plugin **************/
function uvasomrfd_styles() {
	wp_enqueue_style( 'uvasomrfd', plugins_url(). '/uvasomrfd/uvasomrfd.css');
}    
add_action('wp_enqueue_scripts', 'uvasomrfd_styles');
/***********add class to body tag for styling**************/
function uvasomrfd_add_classes( $classes ) {
	$classes[] = 'rfd';
	return $classes;
}
add_filter( 'body_class', 'uvasomrfd_add_classes' );
/***********customizes WP search box to only show faculty listings**************/
/* sort search results by title */
function sort_searchresult_by_title($k) {
if(is_search()) {
$k->query_vars['orderby'] = 'title';
$k->query_vars['order'] = 'ASC';
}
}
add_action('pre_get_posts','sort_searchresult_by_title');

//********Function to redirect to faculty search results template***********//
add_action("template_redirect", 'uvasomrfd_listing_redirect');
function uvasomrfd_listing_redirect() {
	global $post;
	$plugindir = dirname( __FILE__ );
	$archivetemplate = 'uvasomrfd_archive.php';
	$singletemplate = 'uvasomrfd_listing_single.php';
	//See if it is a search result
	if (is_tax( 'primary')||is_tax( 'training-grant')||is_tax( 'other-affs')||is_tax( 'research-discipline')||is_page('27718'))
	//if (strpos($_SERVER["REQUEST_URI"], 'faculty-listing') || is_tax( 'primary')||is_tax( 'training-grant'))
		{
			include($plugindir . '/' . $archivetemplate);
		}
	if ( is_single($post)&& (get_post_type( $post )) == 'faculty-listing')
	//if (strpos($_SERVER["REQUEST_URI"], 'faculty-listing') || is_tax( 'primary')||is_tax( 'training-grant'))
		{
			include($plugindir . '/' . $singletemplate);
		}

}
//********Function to concatenate all custom fields to content area for rss feeds***********//
function uvasomrfd_fields_in_feed($content) {  
    if(is_feed()) {  
        $post_id = get_the_ID();  
        $output .= '&lt;h2&gt;'.get_post_meta($post_id, 'wpcf-first-name', true).' ';
		$middle = get_post_meta($post_id, 'wpcf-middle-name', true);
		if (!empty($middle)){
			$output .= get_post_meta($post_id, 'wpcf-middle-name', true).' ';
		}
		$output .= get_post_meta($post_id, 'wpcf-last-name', true).'&lt;/h2&gt;'."\n";
        $output .= '&lt;h4&gt;Education&lt;/h4&gt;'."\n";
        $output .= '&lt;ul&gt;'."\n";
        $output .= get_post_meta($post_id, 'wpcf-degrees-earned', true)."\n";
        $output .= '&lt;/ul&gt;'."\n";
        $output .= '&lt;h4 class=&quot;faculty&quot;&gt;Primary Appointment&lt;/h4&gt;'."\n";
        $output .= get_post_meta($post_id, 'wpcf-degrees-earned', true).', '.wp_get_post_terms( $post_id, 'primary')."\n";
        $output .= '&lt;h4 class=&quot;faculty&quot;&gt;Contact&lt;/h4&gt;'."\n";
        $output .= 'Email: <a href="mailto:'. get_post_meta($post_id, 'wpcf-email', true). '">'.get_post_meta($post_id, 'wpcf-email', true).'</a>'."\n";
        $output .= '&lt;h4&gt;Research Interests&lt;/h4&gt;'."\n";
        $output .= get_post_meta($post_id, 'wpcf-research-interest-title', true)."\n";
        $output .= '&lt;h4&gt;Research Description&lt;/h4&gt;'."\n";
		//$output .= get_the_content()."\n";
        $output .= '&lt;h4 class=&quot;publications&quot; id="'.get_post_meta($post_id, 'wpcf-curv_id', true).'">Selected Publications&lt;/h4&gt;'."\n";
        $output .= '&lt;ul class=&quot;facpublications&quot;&gt;&lt;/ul&gt;'."\n";
        $content = $content.$output;  
    }  
    return $content;  
}  
add_filter('the_content','uvasomrfd_fields_in_feed');
//search form
function get_terms_dropdown($taxonomies, $args){
	$myterms = get_terms($taxonomies, $args);
	$output ="<select name='primary'>";
	foreach($myterms as $term){
		$root_url = get_bloginfo('url');
		$term_taxonomy=$term->taxonomy;
		$term_slug=$term->slug;
		$term_name =$term->name;
		$link = $term_slug;
		$output .="<option value='".$link."'>".$term_name."</option>";
	}
	$output .="</select>";
return $output;
}
// Default post thumbnail
add_filter('genesis_get_image', 'uvasomrfd_image_fallback', 10, 2);
	function uvasomrfd_image_fallback($output, $args) {
			global $post;
			if( $output || $args['size'] == 'full' )
					return $output;
			
			$thumbnail = '/sharedassets/images/blankavatar.jpg';
			
			switch($args['format']) {
			
					case 'html' :
							return '<img src="'.$thumbnail.'" class="alignleft post-image entry-image" alt="'. get_the_title($post->ID) .'" />';
							break;
					case 'url' :
							return $thumbnail;
							break;
				 default :
						 return $output;
							break;
			}
	}
?>
