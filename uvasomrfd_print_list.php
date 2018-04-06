<?php
function uvasomrfd_faculty_printlist() {
	
	/*if( have_posts() ):
		while( have_posts() ): the_post(); */
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
		
		/*endwhile;
		genesis_posts_nav();
	endif;*/
}
?>