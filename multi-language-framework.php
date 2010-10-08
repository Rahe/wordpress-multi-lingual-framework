<?php
/*
Plugin Name: Multi Language Framework
Plugin URI: http://reddes.bvsalud.org/projects/multi-language-framework/
Description: Handles creation of multilingual content.
Author: BIREME, LeoGermani
Version: 0.1
*/

define( 'MLF_VERSION', '0.1' );

$plugin_folder  = plugin_basename( dirname(__FILE__) );
$plugin_url     = WP_CONTENT_URL . '/plugins/'. $plugin_folder .'/'; 
$plugin_prefix  = 'mlf_'; 
$plugin_name    = 'Multi Language Framework';
$admin_language = '';

// Load multi language framework files
require_once(dirname(__FILE__) . "/config.php");
require_once(dirname(__FILE__) . "/settings.php");
require_once(dirname(__FILE__) . "/core-functions.php");
require_once(dirname(__FILE__) . "/utils.php");
require_once(dirname(__FILE__) . "/edit_screen.php");
require_once(dirname(__FILE__) . "/wp_options.php");


function mlf_init() {
    global $admin_language;
    
    // extract url information
    $url_info = mlf_extractURL($_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
    $default_language = mlf_get_option('default_language');
    $auto_update_mo = mlf_get_option('auto_update_mo');
    
    // check cookies for admin
    if(defined('WP_ADMIN')) {
        if(isset($_GET['lang']) && mlf_isEnabled($_GET['lang'])) {
            $admin_language  = $url_info['language'];
            setcookie('mlf_admin_language', $admin_language, time()+60*60*24*30);
        } elseif(isset($_COOKIE['mlf_admin_language']) && mlf_isEnabled($_COOKIE['mlf_admin_language'])) {
            $admin_language = $_COOKIE['mlf_admin_language'];
        } else {
            $admin_language = $default_language;
        }
    } else {
        $admin_language = $url_info['language'];
    }
    
    // load plugin translations
    load_plugin_textdomain('mlf', false, dirname(plugin_basename( __FILE__ )).'/languages');
    
    // register plugin javascript files
    mlf_add_js();
    
    // register plugin css files
    mlf_add_css();
    
    // update Gettext Databases if on Backend
    if(defined('WP_ADMIN') && $auto_update_mo){
        mlf_updateGettextDatabases();    
    }
}

function mlf_activate() {
    $admin = get_role('administrator');   
    $admin->add_cap('manage-multi-language-framework');
    
    // register on options database plugin default settings 
    create_default_settings();

}

        
function mlf_deactivate() {
    global $wpdb, $plugin_prefix;
    
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$plugin_prefix}%'");
}
    
    
function mlf_admin_menu() {
    global $plugin_url;
    
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
        add_menu_page(__($language_name[$language], 'mlf'), __($language_name[$language], 'mlf'), 'read', $link, NULL, $plugin_url . $flag_location . $flag[$language]);
    }
    
    //call register settings function
    add_action( 'admin_init', 'mlf_register_settings' );
}

function mlf_add_js() {
    global $plugin_url;
    
    wp_enqueue_script('mlf-admin', $plugin_url . 'js/settings.js');    
}
        
function mlf_add_css() {
    global $plugin_url;
   
    wp_enqueue_style('mlf-admin', $plugin_url . 'css/style.css');
}

function mlf_register_settings(){
    global $plugin_prefix;
    
     $options = array ('default_language', 'url_mode');
    
    foreach($options as $option) {    
        register_setting('multi-language-settings-group', $plugin_prefix . $option);
    }

}    
   
function mlf_get_option($option_name) {
    global $plugin_prefix;
    
    return get_option($plugin_prefix . $option_name);
}   
   
register_activation_hook(__FILE__, 'mlf_activate');
register_deactivation_hook(__FILE__, 'mlf_deactivate');

// Hooks (Actions)
add_action('admin_menu',    'mlf_admin_menu');
add_filter('locale',        'mlf_localeForCurrentLanguage',99);

add_action('plugins_loaded','mlf_init');

?>
