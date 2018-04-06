<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);

class uvasomrfdra_search_widget extends WP_Widget {

	// constructor
	function uvasomrfdra_search_widget() {
		parent::WP_Widget(false, $name = __('UVA Research Area Search Widget', 'uvasomrfdra_search_widget') );
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
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('UVA Research Area Search Widget', 'uvasomrfdra_search_widget'); ?></label>
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
        <!--query for faculty research areas-->
        <form action="<?php bloginfo('url')?>/" method="get" class="ugrad">
        <input name="other-affs"/>
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
add_action('widgets_init', create_function('', 'return register_widget("uvasomrfdra_search_widget");'));
?>