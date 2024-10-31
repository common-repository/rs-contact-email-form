<?php

// If uninstall is not called from WordPress, exit 
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) { 
exit(); 
} 

// Delete Option 
$option_name = 'RsContactFormWidget';
delete_option( $option_name ); 

?>