<?php

#var_dump(get_locale());

add_action('init', 'post_translations_init');

// Creates a post type for each language
function post_translations_init() {
   
    global $wp_post_types, $mlf_config;
    
    $language_name = $mlf_config['language_name'];
    $enabled_languages = $mlf_config['enabled_languages'];
    $default_language = $mlf_config['default_language'];
    
    foreach ($enabled_languages as $l) {
        
        if ($l == $default_language)
            continue;
        //var_dump($mlf_config['post_types']);
        foreach ($mlf_config['post_types'] as $p_type) {
            
            $labels = (array) $wp_post_types[$p_type]->labels;
            $labels['name'] .= ' - ' . $language_name[$l];
            $labels['menu_name'] .= ' - ' . $language_name[$l];
            
            switch ($p_type) {
            
                case 'post':
                    $menu_pos = 5;
                    $p_type_supports = array('title','editor','author','thumbnail','excerpt','comments');
                    break;
                case 'page':
                    $menu_pos = 20;
                    $p_type_supports = array('title','editor','author','thumbnail','excerpt','comments', 'page-attributes');
                    break;
                default:
                    $menu_pos = $wp_post_types[$p_type]->menu_position ? $wp_post_types[$p_type]->menu_position : 25;
                    $p_type_supports = array('title','editor','author','thumbnail','excerpt','comments');
            
            }
            
            $args = array(
                'labels' => $labels,
                'public' => true,
                'rewrite' => array('slug' => $l),
                'capability_type' => $wp_post_types[$p_type]->capability_type,
                'hierarchical' => $wp_post_types[$p_type]->hierarchical == 1,
                'menu_position' => $menu_pos,
                'supports' => $p_type_supports
            ); 
            
            //TODO: Post types names can only have 20 chars. Ho to deal with it?
            
            register_post_type($p_type . '_t_' . $l, $args);
            
        }
    }
    
    // Adds the Translation Column to the Edit screen
    
    add_filter("manage_posts_columns", '_post_translations_add_column');
    
    add_action("manage_posts_custom_column", 'post_translations_add_column', 10, 2);
    add_action("manage_pages_custom_column", 'post_translations_add_column', 10, 2);
    
    add_action('admin_menu', 'post_translation_box');
    
    foreach ($mlf_config['post_types'] as $p_type) {
        add_filter("manage_{$p_type}_posts_columns", '_post_translations_add_column');
        add_action("save_$p_type", 'post_translation_save');
    }
    
    
}

function _post_translations_add_column($defaults) {
    global $pagenow;
    
    $enabled_languages = mlf_get_option('enabled_languages');
    
    if(count($enabled_languages) <= 1 || get_query_var('post_status') == 'trash'){
        return $columns;
    }
    
    foreach($defaults as $k=>$v){
        $new_columns[$k] = $v;
        if($k=='title')
            $new_columns['post_translations'] = __('Translations', 'mlf');
    }
    return $new_columns;

}

function post_translations_add_column($column_name, $id) {
    
    if ($column_name=="post_translations") {
        global $wpdb;
        
        $enabled_languages = mlf_get_option('enabled_languages');
        $default_language = mlf_get_option('default_language');
        
        $flag_location = mlf_get_option('flag_location');
        $post_type = get_query_var('post_type');
        $flag = mlf_get_option('flag');
        
        // Quick Edit
        if ($post_type == '' && DOING_AJAX) 
            $post_type = $_POST['post_type'];
        
        $post_type_base = preg_replace('/^(\S+)_t_\S{2}$/', "$1", $post_type);
        
        foreach ($enabled_languages as $lang) {
            
            $translation_id = false;
            $p_type = $post_type_base . '_t_' . $lang;            
            $flag_img = MLF_PLUGIN_URL . $flag_location . $flag[$lang];
            
            if ($p_type == $post_type_base . '_t_' . $default_language)
                $p_type = $post_type_base;

            if ( $post_type == $p_type ) {
                continue;
            }
            
            
            
            #echo "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_type='$p_type' AND meta_key='_translation_of' AND meta_value=$id ";
            
            if ($translation_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='$p_type' AND meta_key='_translation_of' AND meta_value=$id ")) {                
                echo "<a title='Edit' href='post.php?action=edit&post=$translation_id'><span class='icon_edit'><span>edit</span></span> <img src='$flag_img'/></a> ";
            } else {
                echo "<a title='Add' href='post-new.php?post_type=$p_type&translation_of=$id'><span class='icon_add'><span>add</span> </span> <img src='$flag_img'/></a> ";
            }
        }
    }
}

function post_translation_box() {
    $enabled_languages = mlf_get_option('enabled_languages');
    $post_types = mlf_get_option('post_types');
    
    foreach ($post_types as $p) {
    
        add_meta_box( 'post_translations',__('Post Translations', 'mlf'), 'post_translation_inner_box', $p, 'side' );
        add_meta_box( 'mlf_other_version_id',__('Post Translations', 'mlf'),'mlf_other_versions_box', $p, 'normal', 'high' );
        
        foreach ($enabled_languages as $lang) {
            add_meta_box( 'post_translations',__('Post Translations', 'mlf'),'post_translation_inner_box', $p . '_t_' . $lang, 'side' );
            add_meta_box( 'mlf_other_version_id',__('Post Translations', 'mlf'),'mlf_other_versions_box', $p . '_t_' . $lang, 'normal', 'high' );
        }
    
    }
    
    
}
   

function post_translation_inner_box() {

    global $post;
    // Use nonce for verification
    echo '<input type="hidden" name="post_translation_noncename" id="emissoras_noncename" value="' . 
      wp_create_nonce( 'post_translation_noncename' ) . '" />';

    if ($_GET['action'] != 'edit') {
        $translation_of = $_GET['translation_of'];
    } else {
        $translation_of = get_post_meta($post->ID, '_translation_of', true);
    }
    
    
    echo '<input type="hidden" name="_translation_of" value="' . $translation_of . '" >';
    
    
    global $wpdb;
    
    $default_language = mlf_get_option('default_language');
    $enabled_languages = mlf_get_option('enabled_languages');

    #só aparecer links pra criar ou editar traduções quando estiver editando posts
    if ($_GET['action'] != 'edit') {
        _e('Save this post so you can add and edit translations', 'mlf');
        return;
    }
    
    $post_type = $post->post_type;
    $post_type_base = preg_replace('/^(\S+)_t_\S{2}$/', "$1", $post->post_type);
    
    $flag_location = mlf_get_option('flag_location');
    $flag = mlf_get_option('flag');   
    
    foreach ($enabled_languages as $lang) {
        
        $translation_id = false;
        $p_type = $post_type_base . '_t_' . $lang;            
        $flag_img = MLF_PLUGIN_URL . $flag_location . $flag[$lang];
        
        if ($p_type == $post_type_base . '_t_' . $default_language)
            $p_type = $post_type_base;

        if ( $post_type == $p_type ) {
            continue;
        }
        
        
        
        #echo "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='$p_type' AND meta_key='_translation_of' AND meta_value={$post->ID} ";
        
        if ($translation_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='$p_type' AND meta_key='_translation_of' AND meta_value={$post->ID} ")) {                
            echo "<a title='Edit' href='post.php?action=edit&post=$translation_id'><span class='icon_edit'><span>edit</span></span> <img src='$flag_img'/></a> ";
        } else {
            echo "<a title='Add' href='post-new.php?post_type=$p_type&translation_of={$post->ID}'><span class='icon_add'><span>add</span> </span> <img src='$flag_img'/></a> ";
        }
    }
}



# when saving a meta, check if the pair of key value already exists, if so update, if not create
# WordPress core functions for post_meta does not work like that
function mlf_add_post_meta($post_id, $meta_key, $meta_value) {
    global $wpdb;
    
    $meta_value = maybe_serialize( stripslashes_deep($meta_value) );
    
    if ( $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d AND meta_value = %s",
        $meta_key, $post_id, $meta_value ) ) )
        return false;
    
    add_post_meta($post_id, $meta_key, $meta_value);
    
}

function mlf_add_translation_relationship($original, $new) {

    if (!$original || !$new)
        return;
    
    mlf_add_post_meta($original, '_translation_of', $new);
    mlf_add_post_meta($new, '_translation_of', $original);
    
    #var_dump($original, $new); die;

    $also_translation_of = get_post_meta($original, '_translation_of');

    if (is_array($also_translation_of)) {
        foreach ($also_translation_of as $a) {
            if ($a != $new) {
                mlf_add_post_meta($new, '_translation_of', $a);
                mlf_add_post_meta($a, '_translation_of', $new);
            }
        }
    }
    
}

global $mlf_avoid_recursive_save;
$mlf_avoid_recursive_save = false;

function post_translation_save( $post_id ) {

    if ( !wp_verify_nonce( $_POST['post_translation_noncename'], 'post_translation_noncename' )) {
        return $post_id;
    }

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        return $post_id;

    

    #var_dump($_POST['_translation_of']); die;
    
    mlf_add_translation_relationship($_POST['_translation_of'], $post_id);
    
    global $mlf_avoid_recursive_save;
    
    
    if ($_POST['mlf_copy_date'] == 1 && $_POST['_translation_of'] && $mlf_avoid_recursive_save === false) {
        $mlf_avoid_recursive_save = true;
        $from = get_post($_POST['_translation_of'], 'ARRAY_A');
        #$to = get_post($post_id, 'ARRAY_A');
        
        #$to['post_date'] = $from['post_date'];
        #$to['post_date_gmt'] = $from['post_date_gmt'];
        
        #print_r($to); die;
        
        #wp_insert_post($to); // this will create an endless loop, we can avoid it with a global variable or:
        
        global $wpdb;
        #$wpdb->update($wpdb->posts, $to, $to['ID']);
        mysql_query("UPDATE $wpdb->posts SET post_date = '" . $from['post_date'] . "', post_date_gmt = '" . $from['post_date_gmt'] . "' WHERE ID = " . $post_id);
              
    }
    
    return $post_id;
}

add_action('post_submitbox_misc_actions', 'mlf_copy_date_checkbox');
add_action('page_submitbox_misc_actions', 'mlf_copy_date_checkbox');

function mlf_copy_date_checkbox() {
    global $pagenow;
    
    if ($pagenow == 'post-new.php' && $from = $_GET['translation_of']) {
    
        echo '<br /><input type="checkbox" name="mlf_copy_date" value="1" checked>';
        _e('Copy the date of the original post when I first save this translation', 'mlf');
        
    }

}

function mlf_other_versions_box(){
    global $post;

    $default_language = mlf_get_option('default_language');
    $flag_location = mlf_get_option('flag_location');
    $flag = mlf_get_option('flag');

    $edit_post = $post;

    $posts = new WP_Query('meta_key=_translation_of&meta_value=' . $post->ID . '&post_type=any');

    if ( $posts->have_posts() ){
        $translation_version = array();

        while ( $posts->have_posts() ){
            $posts->the_post();
            
            if (preg_match('/^\S+_t_(\S{2})$/', $post->post_type))
                $lang = preg_replace('/^\S+_t_(\S{2})$/', "$1", $post->post_type);
            else
                $lang = $default_language;
                
            $translation_version[$lang] = '<h2>' . get_the_title() . '</h2>' . get_the_content();
        }

        $post = $edit_post;

    ?>
        <div class="translation_div">
            <ul class="translation_tabs">
                <?php
                    foreach ($translation_version as $lang => $text){
                        $flag_img = MLF_PLUGIN_URL . $flag_location . $flag[$lang];
                        echo '<li class="' . $class . '"><a href="#post_translation_'. $lang . '"><img src="' . $flag_img . '"></a></li>';
                    }
                 ?>
            </ul>
            <div class="post_translation_container">
                <?php
                    foreach ($translation_version as $lang => $text){
                        echo '<div id="post_translation_' . $lang .'" class="translation_content"> ';
                        echo apply_filters('the_content', $text);
                        echo '</div>';
                    }
                 ?>
            </div>
       </div> 
    <?php

    }

    $post = $edit_post;
}

