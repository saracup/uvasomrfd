<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
/**
 * This file handles the faculty search results page.
*/
add_action( 'wp_enqueue_scripts', 'uvasomrfd_accordion',5 );
require_once(dirname( __FILE__ ). '/uvasomrfd_print_list.php');

/*********Make it sidebar content layout.**************/
if (is_tax( 'primary')||is_tax( 'training-grant')||is_tax( 'other-affs')||is_tax( 'research-discipline')||is_page('27718')){
add_filter('genesis_pre_get_option_site_layout', '__genesis_return_sidebar_content');
}
/*********Don't display the post meta after each post.**************/
remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
/*********Add the search class to the page body for optional theme styling**************/
function uvasomrfdsearch_add_classes( $classes ) {
	unset($classes['home']);
	$classes[] = 'search';
	$classes[] = 'uvasom-fac-list';
	if (is_page('27718')):$classes[] = 'unleash';
	endif;
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
	if(!is_page('27718')){
	//if (!strpos($_SERVER["REQUEST_URI"], 'faculty-mentoring-undergraduates')) {
	if (is_tax( 'primary')||is_tax( 'training-grant')) {$preterm='Department of ';}
	if (is_tax( 'research-discipline')) {$preterm='Research Discipline: ';}
	if (is_tax( 'training-grant')) {$preterm='Training Program: ';}
	//if (strpos($_SERVER["REQUEST_URI"], '?undergraduates')){$preterm='Faculty Accepting Undergraduates';$term->name='';}
$title = sprintf( '<div class="clearfix"></div><div id="uvasom_page_title">'.genesis_do_breadcrumbs().'<h1 class="archive-title">%s %s</h1>', apply_filters( 'genesis_search_title_text', __( $preterm, 'genesis' ) ), $term->name).'</div>';
	echo apply_filters( 'genesis_search_title_output', $title ) . "\n";
	}
}
/*********Remove the default archive listing **************/
remove_action( 'genesis_loop', 'genesis_do_loop' );
/*********include the custom faculty listing archive listing **************/
if (is_tax( 'primary')||is_tax( 'training-grant')||is_tax( 'other-affs')||is_tax( 'research-discipline')){
add_action('genesis_loop','uvasomrfd_do_loop');
}
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
	if( $loop->have_posts() ):
		while( $loop->have_posts() ): $loop->the_post(); 
	uvasomrfd_faculty_printlist();	
	endwhile;
		genesis_posts_nav();
	endif;
	wp_reset_query();
}

/*********include the custom faculty listing archive listing **************/
if (is_page('27718')){
//add_action('genesis_loop','uvasomrfd_undergrads_do_loop');
add_action('genesis_loop','uvasomrfd_undergradsrd_do_loop');
}
/*********function defining display custom faculty listing archive listing **************/
function uvasomrfd_undergrads_do_loop() {
		global $post;
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;		
		$facargs2 = array(
		'post_type'      => 'faculty-listing',
		'post_status'    => 'publish',
		'orderby'	=> 'title',
		'order'	=> 'ASC',
		'posts_per_page' => 100,
		'paged' => $paged,
			'meta_query' => array(
				'relation' => 'AND',
				   array(
					   'key' => 'wpcf-will-take-undergrads',
					   'value' => '1',
					   'compare' => '=',
				   )
			   )
);
	$loop = new WP_Query( $facargs2 );
	if( $loop->have_posts() ):
		while( $loop->have_posts() ): $loop->the_post(); 
	uvasomrfd_faculty_printlist();	
	endwhile;
		genesis_posts_nav();
	endif;
	wp_reset_query();
}
/************function defining display of research disciplines accepting undergrads **********/
function uvasomrfd_undergradsrd_do_loop() {
  //Retrieve custom taxonomy terms using get_terms and the custom post type.
    $taxonomy = get_terms('research-discipline');
	global $post;
   //Iterate through each term
?>
<div id="accordion"><!--begin accordion container div-->
<?php
    foreach ( $taxonomy as $t ) :
    ?>
	  <?php //Use $category->slug to retrieve the slug. ?>
            <?php
           //Setup the query to retrieve the posts that exist under each term
            $posts = get_posts(array(
              'post_type' => 'faculty-listing',
              'orderby' => 'wpcf-last-name',
              'order' =>  'ASC',
              'taxonomy' => $t->taxonomy,
              'term'  => $t->slug,
			  'meta_key' => 'wpcf-will-take-undergrads',                    //(string) - Custom field key.
			  'meta_value' => '1',                //(string) - Custom field value.
			  'meta_value_num' => 1,                 //(number) - Custom field value.
			  'meta_compare' => '=',     
              'nopaging' => true,
              ));
            // Here's the second, nested foreach loop that cycles through the posts associated with this category
			$the_query = new WP_Query( $posts );
			$count = $the_query->found_posts;
?>          
<h3 class="found_<?php echo $count; ?>"><?php echo $t->name; ?></h3>
<div class="accordion-section">
<?php		
            foreach($posts as $post) :
              setup_postdata($post); ////set up post data for use in the loop (enables the_title(), etc without specifying a post ID--as referenced in the stackoverflow link above)
            uvasomrfd_faculty_printlist();
            endforeach;
			?>
</div><!--end accordion section div-->
<?php			
	endforeach; 
	wp_reset_postdata();
  ?>
</div><!--end accordion container div-->
<?php
}
//add local accordion script to pages for unleash
function uvasomrfd_accordion(){
wp_enqueue_script('jquery-ui-core','',true,'jquery');
wp_enqueue_script('jquery-ui-accordion','',true,'jquery-ui');
wp_enqueue_script( 'uvasomrfd-accordion',plugins_url().'/uvasomrfd/uvasom_unleash_accordion.js',array('jquery'),'',true );

}
add_action('wp_footer','uvasomrfd-accordion-section',10);
function uvasomrfd_accordion_section() {
	?>
<script>
  $(function() {
    $( "#accordion" ).accordion();
	heightStyle: "content";
  });
</script>
<?php
}

	
	
