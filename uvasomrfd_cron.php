<?php
 
add_action('pmxi_before_xml_import', 'uvasomrfd_before_primary_import', 10, 1);
 
function uvasomrfd_before_primary_import($import_id) {
  $terms = get_terms( 'primary', array( 'fields' => 'ids', 'hide_empty' => false ) );
foreach( $terms as $value ) {
wp_delete_term( $value, 'primary' );
}
}
 
?>