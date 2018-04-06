<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);

class uvasomrfdug_search_widget extends WP_Widget {

	// constructor
	function uvasomrfdug_search_widget() {
		parent::WP_Widget(false, $name = __('UVA UG Search Widget', 'uvasomrfdug_search_widget') );
	}
	//get the dropdowns by taxonomy

	// widget form creation
function form($instance) {

// Check values
if( $instance) {
     $title = esc_attr($instance['title']);
} else {
     $title = 'Search';
}
?>

<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('UVA UG Search Widget', 'uvasomrfdug_search_widget'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>
<?php
	}
	// update widget
	function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
     return $instance;
}

	// display widget
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	   //$text = $instance['text'];
	   echo $before_widget;
	   // Display the widget
	   echo '<div class="widget-text uvasom_faculty_search_widget_box">';
	
	   // Check if title is set
	   if ( $title ) {
		  echo $before_title . $title . $after_title;
	   }
	   //output the search form
	   ?>
        <!--query for faculty taking undergraduate students-->
        <?php 
		    global $wpdb;
			$uvasomrfdquerystr = "SELECT $wpdb->posts.* 
			FROM $wpdb->posts, $wpdb->postmeta
			WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
			AND $wpdb->postmeta.meta_key = 'wpcf-will-take-undergrads' 
			AND $wpdb->postmeta.meta_value = '1' 
			AND $wpdb->posts.post_status = 'publish' 
			AND $wpdb->posts.post_type = 'faculty-listing'
			ORDER BY $wpdb->posts.title ASC";
		 	//$wpdb->get_results($uvasomrfdquerystr, OBJECT);
 
 ?>
        <form action="<?php bloginfo('url')?>/faculty-listing/<?php $wpdb->get_results($uvasomrfdquerystr)?>" method="get" class="ugrad">
        <h5><input type="checkbox" name="wpcf-will-take-undergrads" value="1" />Will take Undergraduate Students</h5>
        <input type="submit" name="submit" value="Search" />
		</form>
        <?php

        ?>
		</form>
<?php
	   echo '</div>';
	   echo $after_widget;
	}
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("uvasomrfdug_search_widget");'));
?>