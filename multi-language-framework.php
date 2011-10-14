<?php
/*
Plugin Name: Multi Language Framework
Plugin URI: https://github.com/Rahe/wordpress-multi-lingual-framework
Description: Handles creation of multilingual content.
Author: Rahe
Version: 0.3

Originally from BIREME, LeoGermani
*/

define( 'MLF_VERSION', '0.3' );
define( 'MLF_PLUGIN_URL', plugins_url('/', __FILE__) );
define( 'MLF_DIR', dirname(__FILE__) );
define( 'MLF_OPTION_CONFIG', 'mlf_config' );
define( 'MLF_OPTION_DEFAULT', 'mlf_default' );

require_once( MLF_DIR . "/inc/class.rewrite.php" );
require_once( MLF_DIR . "/inc/class.widget.php" );
require_once( MLF_DIR . "/inc/class.client.php" );
require_once( MLF_DIR . "/inc/class.admin.php" );
require_once( MLF_DIR . "/inc/class.admin.page.php" );
require_once( MLF_DIR . "/inc/class.post-types.php" );
require_once( MLF_DIR . "/inc/class.post-type.php" );
require_once( MLF_DIR . "/inc/class.widget.php" );
require_once( MLF_DIR . "/inc/functions.tpl.php" );
require_once( MLF_DIR . "/inc/functions.inc.php" );

register_activation_hook(__FILE__, 'mlf_activate');
register_deactivation_hook(__FILE__, 'mlf_deactivate');

add_action('plugins_loaded','mlf_init');
function mlf_init() {
	global $mlf;
	
	$mlf['rewrite'] = new MLF_Rewrite();
	$mlf['client'] = new MLF_Client();
	
	if( is_admin() ) {
		$mlf['admin'] = new MLF_Admin();
		$mlf['admin-page'] = new MLF_Admin_Page();
	}
	
	$mlf['post-types'] = new MLF_PostTypes();
}

?>
