<?php

function mlf_isEnabled($lang) {
    
    $enabled_languages = mlf_get_option('enabled_languages');
    
    return in_array($lang, $enabled_languages);
}

function mlf_parseURL($url) {
    $r  = '!(?:(\w+)://)?(?:(\w+)\:(\w+)@)?([^/:]+)?';
    $r .= '(?:\:(\d*))?([^#?]+)?(?:\?([^#]+))?(?:#(.+$))?!i';

    preg_match ( $r, $url, $out );
    $result = @array(
        "scheme" => $out[1],
        "host" => $out[4].(($out[5]=='')?'':':'.$out[5]),
        "user" => $out[2],
        "pass" => $out[3],
        "path" => $out[6],
        "query" => $out[7],
        "fragment" => $out[8]
        );
    return $result;
}

function mlf_startsWith($s, $n) {
    if(strlen($n)>strlen($s)) return false;
    if($n == substr($s,0,strlen($n))) return true;
    return false;
}

function mlf_get_tranlsations_ids($post_id, $post_type = 'post') {

    if (!is_numeric($post_id))
        return false;
    
    global $wpdb;
    
    $post_type_base = preg_replace('/(.+)_translations_([a-zA-Z]{2})/', "$1", $post_type);
    
    $enabled_languages = mlf_get_option('enabled_languages');
    $default_language = mlf_get_option('default_language');
    $result = array();
    
    foreach ($enabled_languages as $lang) {
            
        $translation_id = false;
        $p_type = $post_type_base . '_translations_' . $lang;            
        
        if ($p_type == $post_type_base . '_translations_' . $default_language)
            $p_type = $post_type_base;

        if ( $post_type == $p_type ) {
            continue;
        }
        
        $result[$lang] = $wpdb->get_var("SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='$p_type' AND meta_key='_translation_of' AND meta_value=$post_id ");
        
    }
    
    return $result;
    
}

?>
