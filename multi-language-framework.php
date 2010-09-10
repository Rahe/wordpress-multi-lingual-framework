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

// Load multi language framework files
require_once(dirname(__FILE__) . "/config.php");
require_once(dirname(__FILE__) . "/settings.php");
require_once(dirname(__FILE__) . "/core-functions.php");
require_once(dirname(__FILE__) . "/utils.php");
require_once(dirname(__FILE__) . "/edit_screen.php");


function mlf_init() {
    global $mlf_config;
    
    // extract url information
    $mlf_config['url_info'] = mlf_extractURL($_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
    
    // check cookies for admin
    if(defined('WP_ADMIN')) {
        if(isset($_GET['lang']) && mlf_isEnabled($_GET['lang'])) {
            $mlf_config['language'] = $mlf_config['url_info']['language'];
            setcookie('mlf_admin_language', $mlf_config['language'], time()+60*60*24*30);
        } elseif(isset($_COOKIE['mlf_admin_language']) && mlf_isEnabled($_COOKIE['mlf_admin_language'])) {
            $mlf_config['language'] = $_COOKIE['mlf_admin_language'];
        } else {
            $mlf_config['language'] = $mlf_config['default_language'];
        }
    } else {
        $mlf_config['language'] = $mlf_config['url_info']['language'];
    }
    
    // load plugin translations
    load_plugin_textdomain('mlf', false, dirname(plugin_basename( __FILE__ )).'/languages');
    
    // register plugin javascript files
    mlf_add_js();
    
    // register plugin css files
    mlf_add_css();
    
    // update Gettext Databases if on Backend
    if(defined('WP_ADMIN') && $mlf_config['auto_update_mo']){
        mlf_updateGettextDatabases();    
    }    
}

function mlf_activate() {
    $admin = get_role('administrator');   
    $admin->add_cap('manage-multi-language-framework');
}

        
function mlf_deactivate() {
    global $wpdb, $plugin_prefix;
    
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$plugin_prefix}%'");
}
    
    
function mlf_admin_menu() {
    global $mlf_config;
    
    add_submenu_page( 'options-general.php', __('Multi Language Settings', 'mlf'), 'Multi Language', 'manage_options', 'mlf', 
                    'mlf_page_admin');
                
    // generate menu with flags for every enabled language
    foreach($mlf_config['enabled_languages'] as $id => $language) {
        $link = add_query_arg('lang', $language);
        $link = (strpos($link, "wp-admin/") === false) ? preg_replace('#[^?&]*/#i', '', $link) : preg_replace('#[^?&]*wp-admin/#i', '', $link);
        if(strpos($link, "?")===0||strpos($link, "index.php?")===0) {
            if(current_user_can('manage_options')) 
                $link = 'options-general.php?page=multi-language-framework&godashboard=1&lang='.$language; 
            else
                $link = 'edit.php?lang='.$language;
        }
        add_menu_page(__($mlf_config['language_name'][$language], 'mlf'), __($mlf_config['language_name'][$language], 'mlf'), 'read', $link, NULL, $mlf_config['baseurl'] . $mlf_config['flag_location'] . $mlf_config['flag'][$language]);
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
   
    wp_enqueue_style('mlf-admin', $plugin_url . 'css/settings.css');
}

function mlf_register_settings(){
    global $plugin_prefix;
    
     $options = array ('default_language', 'enabled_languages', 'url_mode');
    
    foreach($options as $option) {    
        register_setting('multi-language-settings-group', $plugin_prefix . $option);
    }

}    
   
register_activation_hook(__FILE__, 'mlf_activate');
register_deactivation_hook(__FILE__, 'mlf_deactivate');

// Hooks (Actions)
add_action('admin_menu',    'mlf_admin_menu');
add_filter('locale',        'mlf_localeForCurrentLanguage',99);

add_action('plugins_loaded','mlf_init');

?>
