<?php
global $wpdb, $mlf_options_restore;

$mlf_all_options = $wpdb->get_col("SELECT option_name FROM $wpdb->options");

foreach ($mlf_all_options as $o) {
    if (!preg_match('/^mlf_\S+/', $o)) {
        add_filter('pre_option_' . $o, 'mlf_pre_option');
    }
}

add_action('update_option', 'mlf_update_option', 10, 3);
add_action('updated_option', 'mlf_updated_option', 10, 3);

function mlf_update_option($option, $old_value, $new_value) {

    if (preg_match('/^mlf_\S+/', $option))
        return false;
    
    global $wpdb, $mlf_config, $mlf_options_restore;
    
    $default_language = mlf_get_option('default_language');
    
    if ($mlf_config['current_language'] == $default_language)
        return false;
    
    // We need to save the value before its updated so we can restore it afterwars
    // On the updated_option hook we can not trust $old_value, because its already filtered in the get_option() call
    
    // save value
    $mlf_options_restore[$option] = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = '$option' LIMIT 1");
    }

function mlf_updated_option($option, $old_value, $new_value) {

    if (preg_match('/^mlf_\S+/', $option))
        return false;
    
    global $wpdb, $mlf_config, $mlf_options_restore;
    
    $default_language = mlf_get_option('default_language');
    
    if ($mlf_config['current_language'] == $default_language)
        return false;
    
    // restore old_value
    
    $old_value = $mlf_options_restore[$option];
    
    $wpdb->update( $wpdb->options, array( 'option_value' => $old_value ), array( 'option_name' => $option ) );
    
    //save new value to the language option
    update_option('mlf_' . $mlf_config['current_language'] . '_' . $option, $new_value);


}



function mlf_pre_option($r) {
    
    $option = str_replace('pre_option_', '', current_filter());
    
    global $wpdb, $mlf_config;
    
    $default_language = mlf_get_option('default_language');
    
    if ($mlf_config['current_language'] != $default_language && $value = get_option('mlf_' . $mlf_config['current_language'] . '_' . $option, false) ) {
        return $value;
    } else {
        return false;
    }
    

}

?>
