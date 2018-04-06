<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL);

class uvasomrfd_unleash_widget extends WP_Widget {
// constructor
	function __construct() {
		parent::__construct(
			'uvasomrfd_unleash_widget', // Base ID
			'UVA SOM Faculty Unleash Widget', // Name
			array( 'description' => 'UVA SOM Faculty Unleash Widget' ) // Args
		);

	//function uvasomrfd_unleash_widget() {
		//parent::WP_Widget(false, $name = __('UVA SOM Faculty Unleash Widget', 'uvasomrfd_unleash_widget') );
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
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'uvasomrfd_unleash_widget'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>
<p>
<input class="checkbox" type="checkbox" <?php checked($instance['rd'], 'on'); ?> id="<?php echo $this->get_field_id('rd'); ?>" name="<?php echo $this->get_field_name('rd'); ?>" />
<label for="<?php echo $this->get_field_id('rd'); ?>">Include Research Discipline</label></p>
<p>
<input class="checkbox" type="checkbox" <?php checked($instance['primary'], 'on'); ?> id="<?php echo $this->get_field_id('primary'); ?>" name="<?php echo $this->get_field_name('primary'); ?>" />
<label for="<?php echo $this->get_field_id('primary'); ?>">Include Primary Department</label></p>
<?php
	}
	// update widget
	function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
	  $instance['rd'] = $new_instance['rd'];
	  $instance['primary'] = $new_instance['primary'];
     return $instance;
}

	// display widget
	function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	   $rd = $instance['rd'] ? 'true' : 'false';
	   $primary = $instance['primary'] ? 'true' : 'false';
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
	   <form action="<?php bloginfo('url'); ?>" method="get">
        <input type="submit" name="submit" value="Search" />
        </form>
<?php
	   echo '</div>';
	   echo $after_widget;
	}
}
function unleash_taxonomy_dropdown( $taxonomy, $title ) {
	$terms = get_terms( $taxonomy );
	if ( $terms ) {
	if ($taxonomy == 'primary') {$title = 'Primary Department';}
	if ($taxonomy == 'training-grant') {$title = 'Training Grant';}
	if ($taxonomy == 'research-discipline') {$title = 'Research Discipline';}
		printf( '<select name="%s" class="postform">', esc_attr( $taxonomy ) );
		echo '<option value="" selected="selected">By '.$title.'</option>';
		foreach ( $terms as $term ) {
			printf( '<option value="%s">%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
		print( '</select>' );
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("uvasomrfd_unleash_widget");'));
?>
