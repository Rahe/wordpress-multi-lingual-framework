<?php
/*
Plugin Name: Multi Language Framework
Plugin URI: http://reddes.bvsalud.org/projects/multi-language-framework/
Description: Handles creation of multilingual content.
Author: BIREME, LeoGermani
Version: 0.1
*/

define( 'MLF_VERSION', '0.1' );

$plugin_folder = plugin_basename( dirname(__FILE__) );

// automatically update .mo files
$mlf_config['auto_update_mo'] = true;

$mlf_config['basepath'] = WP_PLUGIN_DIR . '/' . $plugin_folder . '/';
$mlf_config['baseurl']  = WP_CONTENT_URL . '/plugins/'. $plugin_folder .'/'; 
$mlf_config['opt_prefix']  = 'mlf_'; 

$mlf_config['locale']['de'] = "de_DE";
$mlf_config['locale']['en'] = "en_US";
$mlf_config['locale']['zh'] = "zh_CN";
$mlf_config['locale']['fi'] = "fi";
$mlf_config['locale']['fr'] = "fr_FR";
$mlf_config['locale']['nl'] = "nl_NL";
$mlf_config['locale']['se'] = "sv_SE";
$mlf_config['locale']['it'] = "it_IT";
$mlf_config['locale']['ro'] = "ro_RO";
$mlf_config['locale']['hu'] = "hu_HU";
$mlf_config['locale']['ja'] = "ja";
$mlf_config['locale']['es'] = "es_ES";
$mlf_config['locale']['vi'] = "vi";
$mlf_config['locale']['ar'] = "ar";
$mlf_config['locale']['pt'] = "pt_BR";

// Names for languages in the corresponding language, add more if needed
$mlf_config['language_name']['de'] = "Deutsch";
$mlf_config['language_name']['en'] = "English";
$mlf_config['language_name']['zh'] = "中文";
$mlf_config['language_name']['fi'] = "suomi";
$mlf_config['language_name']['fr'] = "Français";
$mlf_config['language_name']['nl'] = "Nederlands";
$mlf_config['language_name']['se'] = "Svenska";
$mlf_config['language_name']['it'] = "Italiano";
$mlf_config['language_name']['ro'] = "Română";
$mlf_config['language_name']['hu'] = "Magyar";
$mlf_config['language_name']['ja'] = "日本語";
$mlf_config['language_name']['es'] = "Español";
$mlf_config['language_name']['vi'] = "Tiếng Việt";
$mlf_config['language_name']['ar'] = "العربية";
$mlf_config['language_name']['pt'] = "Português";


// Flag images configuration
// Look in /flags/ directory for a huge list of flags for usage
$mlf_config['flag']['en'] = 'gb.png';
$mlf_config['flag']['de'] = 'de.png';
$mlf_config['flag']['zh'] = 'cn.png';
$mlf_config['flag']['fi'] = 'fi.png';
$mlf_config['flag']['fr'] = 'fr.png';
$mlf_config['flag']['nl'] = 'nl.png';
$mlf_config['flag']['se'] = 'se.png';
$mlf_config['flag']['it'] = 'it.png';
$mlf_config['flag']['ro'] = 'ro.png';
$mlf_config['flag']['hu'] = 'hu.png';
$mlf_config['flag']['ja'] = 'jp.png';
$mlf_config['flag']['es'] = 'es.png';
$mlf_config['flag']['vi'] = 'vn.png';
$mlf_config['flag']['ar'] = 'arle.png';
$mlf_config['flag']['pt'] = 'br.png';

// Location of flags (needs trailing slash!)
$mlf_config['flag_location'] =  "/flags/";

// enable the use of following languages (order=>language)
$mlf_config['enabled_languages'] = array(
    '0' => 'pt',
    '1' => 'es', 
    '2' => 'en'
);

// Load multi language framework files
require_once(dirname(__FILE__) . "/core-functions.php");
require_once(dirname(__FILE__) . "/utils.php");
//require_once(dirname(__FILE__) . "/edit_screen.php");


function mlf_init() {
    global $mlf_config;

/*       
    // load plugin translations
    load_plugin_textdomain('mlf', false, dirname(__FILE__ ) . '/lang');

    // update Gettext Databases if on Backend
    if(defined('WP_ADMIN') && $mlf_config['auto_update_mo'])
        mlf_updateGettextDatabases(true);    
*/
}

function mlf_activate() {
    $admin = get_role('administrator');   
    $admin->add_cap('manage-multi-language-framework');
}

        
function mlf_deactivate() {
    global $wpdb;
    
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'mlf_%'");
}
    
    
function mlf_admin_menu() {
    global $mlf_config;
    
    add_submenu_page( 'plugins.php','Multi Language Framework Configuration',
                    'Multi Language Framework Configuration', 'manage_options', 'mlf', 
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
}

function mlf_page_admin()
{
     ?>
     <div class="wrap">
           <h2>Multi Language Framework Configuration</h2>
           <p>On this page, you will configure all the aspects of this plugins.</p>
           <form action="" method="post" id="multi-language-framework-form">
                <h3><label for="copyright_text">Default language:</label></h3>
                <p><input type="text" name="default_lang" id="default_lang" value="<?php echo esc_attr( get_option('mlf_default_lang') )?> " /></p>
                <p class="submit"><input type="submit" name="submit"   value="Update options &raquo;" /></p>
                <?php wp_nonce_field('multi_language_admin_options-update'); ?>
           </form>
     </div>
     <?php
}
   
register_activation_hook(__FILE__, 'mlf_activate');
register_deactivation_hook(__FILE__, 'mlf_deactivate');

// Hooks (Actions)
add_action('wp_head',       'mlf_header');

add_action('admin_menu',    'mlf_admin_menu');
add_action('init', 'mlf_init');

?>
