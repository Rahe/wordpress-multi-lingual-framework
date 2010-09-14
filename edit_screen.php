<?php

/*
 * Coloque esse arquivo no functions.php de uma instalação nova do wordpress
 * 
 * Ele só funciona pra posts por enquanto, vai criar um Posts - ES e um Posts - EN
 * 
 * Em cada listagem vc vai poder criar versões nos outros dois idiomas
 * 
 * Proximos passos: Colocar um box na pagina de edição do post com o conteúdo do post nas outras linguas, para o tradutor ter de referencia
 * 
 * No box Traducoes, q fica do lado direito na pagina de edição, colocar o titulo dos posts nas outras linguas (quando houver)
 * 
 * Integrar com os idiomas gerados pelo admin do plugin
 * 
 * Criar automaticamente o mesmo esquema para Paginas
 * 
 */


// CONFIGS //
global $langs; 
$langs = array('en', 'es');

global $defaultLanguage;
$defaultLanguage = 'pt';
// CONFIGS //

add_action('init', 'post_translations_init');

// Creates a post type for each language
function post_translations_init() {
    global $langs;
   
    $language_name = mlf_get_option('language_name');

    $labels = array(
        'name' => _x('Post Translations', 'post type general name'),
        'singular_name' => _x('Post Translation', 'post type singular name'),
        'add_new' => _x('Add New','mlf'),
        'add_new_item' => __('Add New Translation'),
        'edit_item' => __('Edit Translation'),
        'new_item' => __('New Translation'),
        'view_item' => __('View Translation'),
        'search_items' => __('Search Translations'),
        'not_found' =>  __('No translations found'),
        'not_found_in_trash' => __('No translations found in Trash'), 
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'rewrite' => false,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => 5,
        'supports' => array('title','editor','author','thumbnail','excerpt','comments')
    ); 
    
    foreach ($langs as $l) {
        
        $labels = array(
            'name' => _x('Posts - ' . $language_name[$l], 'post type general name'),
            'singular_name' => _x('Post - ' . $l, 'post type singular name'),
            'add_new' => __('Add New', 'mlf'),
            'add_new_item' => __('Add New Translation', 'mlf'),
            'edit_item' => __('Edit Translation','mlf'),
            'new_item' => __('New Translation', 'mlf'),
            'view_item' => __('View Translation', 'mlf'),
            'search_items' => __('Search Translations', 'mlf'),
            'not_found' =>  __('No translations found', 'mlf'),
            'not_found_in_trash' => __('No translations found in Trash', 'mlf'), 
            'parent_item_colon' => ''
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => 5,
            'supports' => array('title','editor','author','thumbnail','excerpt','comments')
        ); 
        register_post_type('post_translations_' . $l, $args);
    }
}


// Adds the Translation Column to the Edit screen

add_action('manage_posts_custom_column', 'post_translations_add_column', 10, 2);
add_filter('manage_posts_columns', '_post_translations_add_column');

function _post_translations_add_column($defaults) {
    global $langs, $pagenow;
    
    if(count($langs) <= 1 || get_query_var('post_status') == 'trash'){
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
        global $langs, $wpdb, $defaultLanguage, $plugin_url;
        
        $flag_location = mlf_get_option('flag_location');
        $post_type = get_query_var('post_type');
        $flag = mlf_get_option('flag');
        
        foreach ($langs as $lang) {
            
            $translation_id = false;
            $p_type = 'post_translations_' . $lang;            
            $flag_img = $plugin_url . $flag_location . $flag[$lang];
            
            if ($post_type != 'post') {
                // instead of checking for the current language, lets check for the default language
                if ($p_type == $post_type) {
                    $lang = $defaultLanguage;
                    $p_type = 'post';
                }
            } 
            
            #echo "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_type='$p_type' AND meta_key='_translation_of' AND meta_value=$id ";
            
            if ($translation_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_type='$p_type' AND meta_key='_translation_of' AND meta_value=$id ")) {                
                echo "<a href='post.php?action=edit&post=$translation_id'>edit <img src='$flag_img'/></a> ";
            } else {
                echo "<a href='post-new.php?post_type=$p_type&translation_of=$id'>add <img src='$flag_img'/></a> ";
            }
        }
    }
}

add_action('admin_menu', 'post_translation_box');
add_action('save_post', 'post_translation_save');

function post_translation_box() {
    global $langs;
    
    add_meta_box( 'post_translations',__('Post Translations', 'mlf'),'post_translation_inner_box', 'post', 'side' );
    
    foreach ($langs as $lang) {
        add_meta_box( 'post_translations',__('Post Translations', 'mlf'),'post_translation_inner_box', 'post_translations_' . $lang, 'side' );
    }
}
   

function post_translation_inner_box() {

    global $post;
    // Use nonce for verification
    echo '<input type="hidden" name="post_translation_noncename" id="emissoras_noncename" value="' . 
      wp_create_nonce( 'post_translation_noncename' ) . '" />';

    // The actual fields for data entry   
    if ($_GET['translation_of']) {
        echo '<input type="hidden" name="_translation_of" value="' . $_GET['translation_of'] . '" >';
    }
    
    global $langs, $wpdb, $defaultLanguage, $plugin_url;
    

    #só aparecer links pra criar ou editar traduções quando estiver editando posts
    if ($_GET['action'] != 'edit') {
        _e('Save this post so you can add and edit translations', 'mlf');
        return;
    }
    
    $post_type = $post->post_type;
    $flag_location = mlf_get_option('flag_location');
    $flag = mlf_get_option('flag');   
    
    foreach ($langs as $lang) {
        
        $translation_id = false;
        $p_type = 'post_translations_' . $lang;
        $flag_img = $plugin_url . $flag_location . $flag[$lang];
        
        if ($post_type != 'post') {
            // instead of checking for the current language, lets check for the default language
            if ($p_type == $post_type) {
                $lang = $defaultLanguage;
                $p_type = 'post';
            }
        }
        
        #echo "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_type='$p_type' AND meta_key='_translation_of' AND meta_value={$post->ID} ";
        
        if ($translation_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_type='$p_type' AND meta_key='_translation_of' AND meta_value={$post->ID} ")) {
            echo "<a href='post.php?action=edit&post=$translation_id'>edit <img src='$flag_img'/></a> ";        
        } else {
            echo "<a href='post-new.php?post_type=post_translations_$lang&translation_of={$post->ID}'>add <img src='$flag_img'/></a> ";
        }   
    }
}


function post_translation_save( $post_id ) {

    if ( !wp_verify_nonce( $_POST['post_translation_noncename'], 'post_translation_noncename' )) {
        return $post_id;
    }

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        return $post_id;

    if ( 'post' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }

    add_post_meta($post_id, '_translation_of', $_POST['_translation_of']);
    add_post_meta($_POST['_translation_of'], '_translation_of', $post_id);
    
    return $post_id;
}
