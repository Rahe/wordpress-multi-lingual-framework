<?php

add_action('pre_get_posts', 'mlf_parse_query');

function mlf_parse_query($wp_query) {

    
    // We dont want to filter posts in the admin
    if (is_admin())
        return;
    
    global $mlf_config;
    
    //var_dump(mlf_get_option('default_language') , $mlf_config['current_language']); 
    $default_language = $mlf_config['default_language'];
    
    //echo '<pre>'; print_r($wp_query);
    
    //if ($default_language == $mlf_config['current_language'])
    //    return;
    
    
    
    if ($wp_query->is_singular != 1) {
    
        if ($default_language == $mlf_config['current_language'])
            return;
        
        $post_type = $wp_query->query_vars['post_type'] ? $wp_query->query_vars['post_type'] : 'post';
        
        if ($post_type != 'nav_menu_item' && $post_type != 'attachment')
            $wp_query->query_vars['post_type'] = $post_type . '_t_' . $mlf_config['current_language'];
    
    } else {
        
        // We are querying a custom post type, we have to help wordPress to know that,
        // because we changed the REQUEST_URI so it doesnt know
        if ($wp_query->query_vars['pagename']) {
            
            //lets check if there are slashes in the page name, that means a child
            $pagename = $wp_query->query_vars['pagename'];
            $pagename = str_replace( dirname($pagename) . '/', '', $pagename);
            
            global $wpdb;
            
            $post_type = $wpdb->get_var( $wpdb->prepare("SELECT post_type FROM $wpdb->posts WHERE post_name = %s", $pagename) );
            
            $wp_query->query_vars['post_type'] = $post_type;
            //$wp_query->query_vars['post_type'] = $post_type . '_t_' . $mlf_config['current_language'];
            $wp_query->query_vars['name'] = $pagename;
            $wp_query->query_vars[$wp_query->query_vars['post_type']] =  $wp_query->query_vars['name'];
            $pagename = '';
            $wp_query->is_page = false;
            $wp_query->is_single = true;


            $wp_query->query = array(
            
                'post_type' => $post_type,
                //'post_type' => $post_type . '_t_' . $mlf_config['current_language'],
                'name' => $pagename,
                $wp_query->query_vars['post_type'] => $wp_query->query_vars['name']
                
            );
            
            // Lets add the template for the original post type in the template hierarchy of this post.
            add_filter('single_template', 'mlf_add_single_template');
            
        }
        
        // We dont have the post ID here, so lets do this in another action
        add_action('template_redirect', 'mlf_single_translation');
    
    }
    
}

function mlf_add_single_template($templates) {

    global $wp_query;
    
    $post_type = $wp_query->query_vars['post_type'];
    
    if (preg_match('/(.+)_t_([a-zA-Z]{2})/', $post_type))
        $orig_post_type = preg_replace('/(.+)_t_([a-zA-Z]{2})/', "$1", $post_type);
    
    $new_templates = array();
    $new_templates[] = "single-$post_type.php";
    $new_templates[] = "single-$orig_post_type.php";
    $new_templates[] = 'single.php';

    return locate_template($new_templates);

}

function mlf_single_translation() {

    global $wp_query;
    $default_language = mlf_get_option('default_language');
    
    if (is_object($wp_query->post) && isset($wp_query->post->ID)) {
    
        global $wpdb, $mlf_config;
        $post = $wp_query->post;
        $post_type = preg_replace('/(.+)_t_([a-zA-Z]{2})/', "$1", $post->post_type);
        
        if (preg_match('/(.+)_t_([a-zA-Z]{2})/', $post->post_type))
            $post_lang = preg_replace('/(.+)_t_([a-zA-Z]{2})/', "$2", $post->post_type);
        else
            $post_lang = $default_language;
        
        // we are seeing the language we want, no need to look for translations
        
        if ($post_lang == $mlf_config['current_language'])
            return;
        
        $post_type_search = $default_language == $mlf_config['current_language'] ? $post_type : $post_type . "_t_" . $mlf_config['current_language'];
        
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

?>
