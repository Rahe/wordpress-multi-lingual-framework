<?php
/*
Plugin Name: Multi Language Framework
Plugin URI: https://github.com/Rahe/wordpress-multi-lingual-framework
Description: Handles creation of multilingual content.
Author: Rahe
Version: 0.1

Originally from BIREME, LeoGermani
*/

define( 'MLF_VERSION', '0.1' );
define( 'MLF_PLUGIN_URL', plugins_url('/', __FILE__) );
define( 'MLF_DIR', dirname(__FILE__) );

require_once( MLF_DIR . "/inc/class.admin.php" );
require_once( MLF_DIR . "/inc/class.admin.page.php" );
require_once( MLF_DIR . "/inc/class.post-types.php" );
require_once( MLF_DIR . "/inc/class.widget.php" );
require_once( MLF_DIR . "/inc/functions.tpl.php" );
require_once( MLF_DIR . "/inc/functions.inc.php" );

// Load multi language framework files
require_once( MLF_DIR . "/default_settings.php" );
//require_once( MLF_DIR . "/settings.php" );
require_once( MLF_DIR . "/functions.php" );
require_once( MLF_DIR . "/parse_query.php" );
//require_once( MLF_DIR . "/post_types.php" );
require_once( MLF_DIR . "/wp_options.php" );
require_once( MLF_DIR . "/widget.php" );

add_action('plugins_loaded','mlf_init');
function mlf_init() {
	global $mlf;
	
	$mlf['admin'] = new MLF_Admin();
	$mlf['admin-page'] = new MLF_Admin_Page();
	
	$mlf['post-types'] = new MLF_PostTypes();
}

function mlf_get_option($option_name) {
	global $mlf_config;
	return $mlf_config[$option_name];
}

register_activation_hook(__FILE__, 'mlf_activate');
register_deactivation_hook(__FILE__, 'mlf_deactivate');

// Hooks (Actions)
add_filter('locale',		'mlf_localeForCurrentLanguage',99);

add_filter('author_feed_link',				'mlf_convertURL');
add_filter('author_link',					'mlf_convertURL');
add_filter('author_feed_link',				'mlf_convertURL');
add_filter('day_link',						'mlf_convertURL');
add_filter('get_comment_author_url_link',	'mlf_convertURL');
add_filter('month_link',					'mlf_convertURL');
add_filter('page_link',						'mlf_convertURL');
add_filter('post_link',						'mlf_convertURL');
add_filter('year_link',						'mlf_convertURL');
add_filter('category_feed_link',			'mlf_convertURL');
add_filter('category_link',					'mlf_convertURL');
add_filter('tag_link',						'mlf_convertURL');
add_filter('term_link',						'mlf_convertURL');
add_filter('the_permalink',					'mlf_convertURL');
add_filter('feed_link',						'mlf_convertURL');
add_filter('post_comments_feed_link',		'mlf_convertURL');
add_filter('tag_feed_link',					'mlf_convertURL');
//add_filter('clean_url',						'mlf_convertURL');


?>
