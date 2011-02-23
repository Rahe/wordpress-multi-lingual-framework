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
    global $admin_language, $mlf_config;
    
    // só fazer isso depois q existir as opções para não dar problema ao ativar o plugin...
    if (mlf_get_option('url_mode')) { 
        
        $mlf_config['url_mode'] = mlf_get_option('url_mode');
        $mlf_config['auto_update_mo'] = mlf_get_option('auto_update_mo');
        $mlf_config['default_language'] = mlf_get_option('default_language');
        $mlf_config['enabled_languages'] = mlf_get_option('enabled_languages');
        $mlf_config['hide_default_language'] = mlf_get_option('hide_default_language');
    }
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
    //load_plugin_textdomain('mlf', false, dirname(plugin_basename( __FILE__ )).'/languages');
    
    // register plugin javascript files
    mlf_add_js();
    
    // register plugin css files
    mlf_add_css();
    
    // update Gettext Databases if on Backend
    if(defined('WP_ADMIN') && $auto_update_mo){
        mlf_updateGettextDatabases();    
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
    
    register_setting('multi-language-settings-group', 'mlf_config');
    
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
add_filter('clean_url',						'mlf_convertURL');

add_action('pre_get_posts', 'mlf_parse_query');

function mlf_parse_query($wp_query) {

    global $mlf_config;
    
    //var_dump(mlf_get_option('default_language') , $mlf_config['language']); 
    $default_language = mlf_get_option('default_language');
    
    //echo '<pre>'; print_r($wp_query);
    
    if ($default_language == $mlf_config['language'])
        return;
    
    
    
    if ($wp_query->is_singular != 1) {
    
        $post_type = $wp_query->query_vars['post_type'] ? $wp_query->query_vars['post_type'] : 'post';
                
        $wp_query->query_vars['post_type'] = $post_type . '_translations_' . $mlf_config['language'];

            
    
    } else {
        
        // We are querying a custom post type, we have to help wordPress to know that,
        // because we changed the REQUEST_URI so it doesnt know
        if ($wp_query->query_vars['pagename']) {
            
            $post_type = $wp_query->query_vars['post_type'] ? $wp_query->query_vars['post_type'] : 'post';
            
            $wp_query->query_vars['post_type'] = $post_type . '_translations_' . $mlf_config['language'];
            $wp_query->query_vars['name'] = $wp_query->query_vars['pagename'];
            $wp_query->query_vars[$wp_query->query_vars['post_type']] =  $wp_query->query_vars['name'];
            $wp_query->query_vars['pagename'] = '';            


            $wp_query->query = array(
            
                'post_type' => $post_type . '_translations_' . $mlf_config['language'],
                'name' => $wp_query->query_vars['pagename'],
                $wp_query->query_vars['post_type'] => $wp_query->query_vars['name']
                
            );
            
            
        }
        
        // We dont have the post ID here, so lets do this in another action
        add_action('template_redirect', 'mlf_single_translation');
    
    }
    
}

function mlf_single_translation() {

    global $wp_query;
    $default_language = mlf_get_option('default_language');
    
    if (is_object($wp_query->post) && isset($wp_query->post->ID)) {
    
        global $wpdb, $mlf_config;
        $post = $wp_query->post;
        $post_type = preg_replace('/(.+)_translations_([a-zA-Z]{2})/', "$1", $post->post_type);
        
        $post_type_search = $default_language == $mlf_config['language'] ? $post_type : $post_type . "_translations_" . $mlf_config['language'];
        
        $query = "select * from $wpdb->posts join $wpdb->postmeta on ID = post_id WHERE post_type = '$post_type_search' 
                AND meta_key = '_translation_of' AND meta_value = $post->ID";
                
        $translation = $wpdb->get_row($query);
        
        if ($translation) {
            $wp_query->post = $translation;
            $wp_query->posts[0] = $translation;
        } else {
            add_filter('the_content', 'mlf_add_not_available_message');
        }
    }
    
    //echo '<pre>'; print_r($wp_query);

}


function mlf_add_not_available_message($content) {

    return "Post nao disponivel nesse idioma <br/><br /> $content";

}

?>
