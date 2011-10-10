<?php
function mlf_activate() {
	$admin = get_role('administrator');   
	$admin->add_cap('manage-multi-language-framework');
	
	// register on options database plugin default settings 
	create_default_settings();

}

function mlf_deactivate() {
	global $wpdb;
	
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'mlf_%'" );
}
?>	