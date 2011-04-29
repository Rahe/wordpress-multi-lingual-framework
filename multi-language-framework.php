<?php
/*
Plugin Name: Multi Language Framework
Plugin URI: http://reddes.bvsalud.org/projects/multi-language-framework/
Description: Handles creation of multilingual content.
Author: BIREME, LeoGermani
Version: 0.1
*/

define( 'MLF_VERSION', '0.1' );


define('MLF_PLUGIN_URL', WP_CONTENT_URL . '/plugins/'. plugin_basename( dirname(__FILE__) ) .'/'); 


// Load multi language framework files
require_once(dirname(__FILE__) . "/default_settings.php");
require_once(dirname(__FILE__) . "/settings.php");
require_once(dirname(__FILE__) . "/functions.php");
require_once(dirname(__FILE__) . "/parse_query.php");
require_once(dirname(__FILE__) . "/post_types.php");
require_once(dirname(__FILE__) . "/wp_options.php");
require_once(dirname(__FILE__) . "/widget.php");

function mlf_init() {
    global $mlf_config;
    
    $mlf_config = array_merge(get_option('mlf_config'), mlf_load_static_options());
    
    /*
    // só fazer isso depois q existir as opções para não dar problema ao ativar o plugin...
    if (mlf_get_option('url_mode')) { 
        
        $mlf_config['url_mode'] = mlf_get_option('url_mode');
        $mlf_config['auto_update_mo'] = mlf_get_option('auto_update_mo');
        $mlf_config['default_language'] = mlf_get_option('default_language');
        $mlf_config['enabled_languages'] = mlf_get_option('enabled_languages');
        $mlf_config['hide_default_language'] = mlf_get_option('hide_default_language');
    }
    * */
    // extract url information
    $mlf_config['url_info'] = mlf_extractURL($_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
    
    // check cookies for admin
    if(defined('WP_ADMIN')) {
        if(isset($_GET['lang']) && mlf_isEnabled($_GET['lang'])) {
            $mlf_config['current_language'] = $mlf_config['url_info']['current_language'];
            setcookie('mlf_admin_language', $mlf_config['current_language'], time()+60*60*24*30);
        } elseif(isset($_COOKIE['mlf_admin_language']) && mlf_isEnabled($_COOKIE['mlf_admin_language'])) {
            $mlf_config['current_language'] = $_COOKIE['mlf_admin_language'];
        } else {
            $mlf_config['current_language'] = $mlf_config['default_language'];
        }
    } else {
        $mlf_config['current_language'] = $mlf_config['url_info']['current_language'];
    }
    
    // load plugin translations
    //load_plugin_textdomain('mlf', false, dirname(plugin_basename( __FILE__ )).'/languages');
    
    if (is_admin()) {
        // register plugin javascript files
        mlf_add_js();
        
        // register plugin css files
        mlf_add_css();
    }    
        
    // remove traces of language (or better not?)
	//unset($_GET['lang']);
    //var_dump($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST']);
	
    $_SERVER['REQUEST_URI'] = $mlf_config['url_info']['url'];
	$_SERVER['HTTP_HOST'] = $mlf_config['url_info']['host'];
	//var_dump($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST']); die;
	// fix url to prevent xss
    //print_r($mlf_config['url_info']); die;
	$mlf_config['url_info']['url'] = mlf_convertURL(add_query_arg('lang',$mlf_config['default_language'],$mlf_config['url_info']['url']));
    
}

function mlf_activate() {
    $admin = get_role('administrator');   
    $admin->add_cap('manage-multi-language-framework');
    
    // register on options database plugin default settings 
    create_default_settings();

}

        
function mlf_deactivate() {
    global $wpdb;
    
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'mlf_%'");
}
    
    
function mlf_admin_menu() {
    
    $language_name = mlf_get_option('language_name');
    $enabled_languages = mlf_get_option('enabled_languages');
    
    $flag = mlf_get_option('flag');
    $flag_location = mlf_get_option('flag_location');
 
    add_submenu_page( 'options-general.php', __('Multi Language Settings', 'mlf'), 'Multi Language', 'manage_options', 'mlf', 
                    'mlf_page_admin');
                
    // generate menu with flags for every enabled language
    foreach( $enabled_languages as $id => $language) {
        $link = add_query_arg('lang', $language);
        $link = (strpos($link, "wp-admin/") === false) ? preg_replace('#[^?&]*/#i', '', $link) : preg_replace('#[^?&]*wp-admin/#i', '', $link);
        if(strpos($link, "?")===0||strpos($link, "index.php?")===0) {
            if(current_user_can('manage_options')) 
                $link = 'options-general.php?page=multi-language-framework&godashboard=1&lang='.$language; 
            else
                $link = 'edit.php?lang='.$language;
        }
        add_menu_page(__($language_name[$language], 'mlf'), __($language_name[$language], 'mlf'), 'read', $link, NULL, MLF_PLUGIN_URL . $flag_location . $flag[$language]);
    }
    
    //call register settings function
    add_action( 'admin_init', 'mlf_register_settings' );
}

function mlf_add_js() {
    
    wp_enqueue_script('mlf-admin', MLF_PLUGIN_URL . 'js/settings.js');    
}
        
function mlf_add_css() {

   
    wp_enqueue_style('mlf-admin', MLF_PLUGIN_URL . 'css/style.css');
}

function mlf_register_settings(){
    
    register_setting('multi-language-settings-group', 'mlf_config');
    
}    
   
function mlf_get_option($option_name) {
    /*
    global $plugin_prefix;
    return get_option($plugin_prefix . $option_name);
    */
    global $mlf_config;
    return $mlf_config[$option_name];
}   
   
register_activation_hook(__FILE__, 'mlf_activate');
register_deactivation_hook(__FILE__, 'mlf_deactivate');

// Hooks (Actions)
add_action('admin_menu',    'mlf_admin_menu');
add_filter('locale',        'mlf_localeForCurrentLanguage',99);

add_action('plugins_loaded','mlf_init');


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
