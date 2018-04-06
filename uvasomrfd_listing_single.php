<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
/**
Template for single faculty lising. Requires Genesis Framework.
 */
/*********add jquery for publications load **************/
function uvasomrfd_pubs() {
	wp_enqueue_script( 'load_pubs', plugins_url(). '/uvasomrfd/loadpubs.js', array('jquery'), '', true );
}    
add_action('wp_enqueue_scripts', 'uvasomrfd_pubs');
/*********Make it sidebar content layout.**************/
add_filter('genesis_pre_get_option_site_layout', '__genesis_return_sidebar_content');
//Filter Post Title to Add Font Class
//////////////////////////////////////////////////////////////////////////////////
//********************CUSTOM TITLE************************************************
//////////////////////////////////////////////////////////////////////////////////
//add_filter('genesis_post_title_output', 'uvasomrfd_alter_post_title');
function uvasomrfd_alter_post_title( $title ) {
		$terms = get_the_terms( $post->ID, 'primary' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$primary_links = array();
			foreach ( $terms as $term ) {
				$primary_links[] = $term->name;
				$primary_slug[] = $term->slug;
			}
			$primary = join( ", ", $primary_links );
			$factitle = $primary_links[0];
			}
    return sprintf( '<h1 class="entry-title">Faculty in '.$primary_links[0].'</h1>', apply_filters( 'genesis_post_title_text', get_the_title() ) );

}
remove_action( 'genesis_post_title','genesis_do_post_title' );
//remove_action('genesis_after_header', 'uvasom_do_post_title');
//add_action('genesis_after_header', 'uvasomrfd_do_post_title');
function uvasomrfd_do_post_title()
{
	echo '<div class="clearfix"></div>';
	echo '<div id="uvasom_page_title">';
	genesis_do_breadcrumbs();
	genesis_do_post_title();
	echo '</div>';
}
//////////////////////////////////////////////////////////////////////////////////
//********************CUSTOM CONTENT LAYOUT***************************************
//////////////////////////////////////////////////////////////////////////////////
//REMOVE POST INFO AND META DISPLAY
remove_action( 'genesis_before_post_content', 'genesis_post_info' );
remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
//REMOVE STANDARD POST CONTENT
remove_action( 'genesis_post_content', 'genesis_do_post_content' );
//ADD FACULTY LISTING POST TYPE CONTENT
add_action( 'genesis_post_content', 'uvasomrfd_single_faclisting' );
/** Add support for Genesis Grid Loop **/
function uvasomrfd_single_faclisting() {
	global $post;
	$middle = get_post_meta( get_the_ID(),'wpcf-middle-name',true );
	$degrees = get_post_meta(get_the_ID(),'wpcf-degrees-earned',true );
	$rank = get_post_meta(get_the_ID(),'wpcf-rank',true );
	$primary  = get_the_terms( $post->ID, 'primary' );
	$restitle = get_post_meta(get_the_ID(),'wpcf-research-interest-title',true );
	$address1 = get_post_meta(get_the_ID(),'wpcf-address1',true );
	$address1 = get_post_meta(get_the_ID(),'wpcf-address2',true );
	$city = get_post_meta(get_the_ID(),'wpcf-city',true );
	$state = get_post_meta(get_the_ID(),'wpcf-state',true );
	$zip = get_post_meta(get_the_ID(),'wpcf-zip',true );
	$tel = get_post_meta(get_the_ID(),'wpcf-campus-phone',true );
	$fax = get_post_meta(get_the_ID(),'wpcf-fax',true );
	$email = get_post_meta(get_the_ID(),'wpcf-email',true );
	$personalurl = get_post_meta(get_the_ID(),'wpcf-personal-website-url',true );
//$resdescription = get_post_meta(get_the_ID(),'wpcf-research-interest-description',true );
	$resdescription = get_the_content();
	$facultylisting = '';
	$facultylisting .= the_post_thumbnail( 'thumbnail' );
	$facultylisting .= '<h2>'.get_post_meta(get_the_ID(), 'wpcf-first-name', true);
	if (!empty($middle)) {
		$facultylisting .= ' '.$middle;
	}
	$facultylisting .= ' '.get_post_meta(get_the_ID(),'wpcf-last-name',true ).'</h2>'."\n";
	//$facultylisting .= '<h2>'.$facultyname.'</h2>'."\n";
	$facultylisting .= '<h4 class="faculty">Primary Appointment</h4>';
		if (!empty($rank)) {
		$facultylisting .= $rank.', ';
		}
		$terms = get_the_terms( $post->ID, 'primary' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$primary_links = array();
			foreach ( $terms as $term ) {
				$primary_links[] = $term->name;
				$primary_slug[] = $term->slug;
			}
			$primary = join( ", ", $primary_links );
			$facultylisting .= '<a href="'.home_url().'/primary/'.$primary_slug[0].'">'.$primary_links[0].'</a>';
			}

	if (!empty($degrees)) {
		$facultylisting .= '<h4 class="faculty">Education</h4>'."\n";
		$facultylisting .= '<ul>'."\n";
		$facultylisting .= html_entity_decode(get_post_meta(get_the_ID(),'wpcf-degrees-earned',true ))."\n";
		$facultylisting .= '</ul>'."\n";
	}
	//$facultylisting .= '<a href="'.get_term_link( $primary->slug, 'primary' ).'/'.$primary.'/">'.$primary.'</a>';
	//$facultylisting .= '<h4 class="faculty">Contact</h4>'."\n";
	$facultylisting .= '<h4 class="faculty">Contact Information</h4>'."\n".'<p>'."\n";
		if (!empty($address1)) {
			$facultylisting .= $address1;
			if (!empty($address2)) {
			$facultylisting .= '<br />'."\n".$address2;
			}
			$facultylisting .= '<br />'."\n".'Charlottesville, VA 22908';
		}
		if (!empty($tel)) {
			$facultylisting .= '<br />'."\n".'<strong>Telephone: </strong><a href="tel:'.$tel.'">'.$tel.'</a>'."\n";
		}
		if (!empty($fax)) {
			$facultylisting .= '<br />'."\n".'<strong>Fax: </strong>'.$fax."\n";
		}
	$facultylisting .= '<br />'."\n".'<strong>Email: </strong><a href="mailto:'.$email.'">'.$email.'</a>'."\n";
	   if (!empty($personalurl)) {
			$facultylisting .= '<br />'."\n".'<strong>Website: </strong><a href="'.$personalurl.'">'.$personalurl.'</a>'."\n";
		}
			$facultylisting .= '</p>';
		if (!empty($restitle)) {
			$facultylisting .= '<h4 class="faculty">Research Interests</h4>'."\n";
			$facultylisting .= '<p>'.$restitle.'</p>'."\n";
		}
		if (!empty($resdescription)) {
			$facultylisting .= '<h4 class="faculty">Research Description</h4>'."\n";
			$facultylisting .= '<div class="researchdesc">'.$resdescription.'</div>'."\n";
		}
	//$facultylisting .= '<span id="'.get_post_meta(get_the_ID(),'wpcf-curv_id',true ).'"></span>'."\n";
	$facultylisting .= '<h4 class="publications">Selected Publications</h4>'."\n";
	$facultylisting .= '<div class="publications_container" id="'.get_post_meta(get_the_ID(),'wpcf-curv_id',true ).'"  style="background-image:url(/sharedassets/images/ajax-loader_large.gif);background-repeat:no-repeat;background-position:center 30px;overflow:hidden;min-height:150px;">'."\n";
	$facultylisting .= ' <div class="publications" id="publications-'.get_post_meta(get_the_ID(),'wpcf-curv_id',true ).'">'."\n";
	$facultylisting .= ' </div>'."\n".'</div>'."\n";
	echo $facultylisting;

}

?>
