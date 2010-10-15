<?php

#add_action('wp', 'mlf_option_filters');


#function mlf_option_filters() {
#echo 'bbbbbbbbbbb';
    global $wpdb;

    $mlf_all_options = $wpdb->get_col("SELECT option_name FROM $wpdb->options");

    foreach ($mlf_all_options as $o) {
        if (!preg_match('/^mlf_\S+_/', $o)) {
      
      
            
            add_filter('pre_option_' . $o, 'mlf_pre_option');
            
            
        }
    }


    add_action('updated_option', 'mlf_update_option', 10, 3);
    #add_filter('get_option', 'mlf_get_option_filter', 10, 3);
    
#}


function mlf_update_option($option, $old_value, $new_value) {

    if (preg_match('/^mlf_\S+_/', $option))
        return false;
    
    global $wpdb, $admin_language;
    
    $default_language = mlf_get_option('default_language');
    
    if ($admin_language == $default_language)
        return false;
    
    // restore old_value
    $wpdb->update( $wpdb->options, array( 'option_value' => $old_value ), array( 'option_name' => $option ) );
    
    //save new value to the language option
    update_option('mlf_' . $admin_language . '_' . $option, $new_value);


}



function mlf_pre_option($r) {

    
    $option = str_replace('pre_option_', '', current_filter());
    
    global $admin_language, $wpdb;
    
    $default_language = mlf_get_option('default_language');
    
    if ($admin_language != $default_language && $value = get_option('mlf_' . $admin_language . '_' . $option, false) ) {
        return $value;
    } else {
        return false;
    }
    

}


/* Não está sendo usada, era a função que usava se fosse usar o novo filtro q eu fiz o patch */
function mlf_get_option_filter($flag, $option, $default) {
    
    if (preg_match('/^mlf_\S+_/', $option))
        return false;
    
    global $admin_language, $wpdb;
    
    $default_language = mlf_get_option('default_language');
    
    #if ($option == 'blogname')
    #    var_dump(get_option('mlf_' . $admin_language . '_' . $option));
    #var_dump( $admin_language , $default_language); die;
    
    if ($admin_language != $default_language && $value = get_option('mlf_' . $admin_language . '_' . $option, false) ) {
        #if ($option == 'blogname') die('aa');
        return $value;
    } else {
        return false;
    }

}
?>
