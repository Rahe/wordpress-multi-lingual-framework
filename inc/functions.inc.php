<?php
function mlf_activate() {
	// Add specific role
	$admin = get_role('administrator');
	$admin->add_cap('manage-multi-language-framework');

	// Names for languages in the corresponding language, add more if needed
	$mlf_default['language_name']['de_DE'] = "Deutsch";
	$mlf_default['language_name']['en_US'] = "English";
	$mlf_default['language_name']['zh_CN'] = "中文";
	$mlf_default['language_name']['fi'] = "Suomi";
	$mlf_default['language_name']['fr_FR'] = "Français";
	$mlf_default['language_name']['nl_NL'] = "Nederlands";
	$mlf_default['language_name']['sv_SE'] = "Svenska";
	$mlf_default['language_name']['it_IT'] = "Italiano";
	$mlf_default['language_name']['ro_RO'] = "Română";
	$mlf_default['language_name']['hu_HU'] = "Magyar";
	$mlf_default['language_name']['ja'] = "日本語";
	$mlf_default['language_name']['es_ES'] = "Español";
	$mlf_default['language_name']['vi'] = "Tiếng Việt";
	$mlf_default['language_name']['ar'] = "العربية";
	$mlf_default['language_name']['pt_BR'] = "Português";

	
	// enable the use of following languages (order=>language)
	$mlf_config['enabled_languages'] = array(
		'0' => 'en_US',
		'1' => 'es_ES', 
		'2' => 'pt_BR'
	);
	
	// Add defualt options
	$mlf_config['hide_default_language'] = true;
	
	// Defualt language
	$mlf_config['default_language'] =  "en_US";
	
	// Te url mode
	$mlf_config['url_mode'] =  "path";
	
	// Pot_types used
	$mlf_config['post_types'] =  array( 'post' );
	
	// update the options
	update_option( MLF_OPTION_CONFIG, $mlf_config );
	update_option( MLF_OPTION_DEFAULT, $mlf_default );
}

/**
 * On desactivation remove the options stating by mlf
 * 
 * @param $query : the query to parse
 * @return false on failure
 * @author Rahe
 */
function mlf_deactivate() {
	global $wpdb;
	
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'mlf_%'" );
}

/**
 * Generate the edit link
 * 
 * @param $tId : the id of the post to get link
 * @param $lang : the lang to get teh translation
 * @return false on failure| string on success
 * @author Rahe
 */
function mlf_translationEditLink( $tId = 0, $lang = '' ) {
	// Get the options
	$languages = get_option( MLF_OPTION_DEFAULT );
	
	// Check the curent language s a translation and id is correctly given
	if( !isset( $languages['language_name'][$lang] ) || !isset( $tId ) || (int)$tId <= 0 )
		return false;
	
	// Return the edit link
	return '<a title="'.__( 'Edit', 'mlf' ).'" href="'.get_edit_post_link( $tId ).'"><span class="icon_edit"><span>'.__( 'Edit', 'mlf' ).'</span></span> '.$languages['language_name'][$lang].'</a></li>';
}

/**
 * Generate the add link
 * 
 * @param $tId : the id of the post to get link
 * @param $pType : the post_type to use as base translation
 * @param $lang : the lang to get teh translation
 * @return false on failure| string on success
 * @author Rahe
 */
function mlf_translationAddLink( $tId, $pType, $lang ) {
	// get the options
	$languages = get_option( MLF_OPTION_DEFAULT );
	
	// Check language is active, check if id given is ok and ost_type exists
	if( !isset( $languages['language_name'][$lang] ) || !isset( $tId ) || (int)$tId <= 0 || !post_type_exists( sanitize_key( $pType ) ) )
		return false;
	
	// Return the add link
	return '<a title="'.__( 'Add', 'mlf' ).'" href="'.admin_url( 'post-new.php?post_type='.$pType.'&translation_of='.$tId ).'"><span class="icon_add"><span>'.__( 'Add', 'mlf' ).'</span> </span> '.$languages['language_name'][$lang].'</a>';
}

/**
 * Get the translations ids from a post_type
 * 
 * @param $post_id : the id of the post
 * @param $post_type : the post_type to use as base translation
 * @return $result|array : array of translated element from this post_type sort by language 
 * @author Rahe
 */
function mlf_get_tranlsations_ids( $post_id, $post_type = 'post' ) {
	global $wpdb;
	
	// check if given id is correct and post_type too
	if ( (int)$post_id <= 0 || !isset( $post_type ) || empty( $post_type ) || !post_type_exists( $post_type ) )
		return false;
	
	// geteh base post_type
	$post_type_base = current( explode( '_t_', $post_type ) );
	
	// get the options and set enabled ad default langauges
	$options = get_option( MLF_OPTION_CONFIG );
	$enabled_languages = isset( $options['enabled_languages'] )?  $options['enabled_languages'] : array() ;
	$default_language = isset( $options['default_language'] )? $options['default_language'] : array() ;
	
	// check there is languages 
	if( empty( $enabled_languages ) )
		return false;
	
	$result = array();
	
	// Get the Id of the translated element for every language
	foreach ( $enabled_languages as $lang ) {
		// Init var
		$translation_id = false;
		
		// Make the post_type translated
		$p_type = sanitize_key( $post_type_base . '_t_' . $lang );
		
		// Check if defualt language
		if ($p_type == sanitize_key( $post_type_base . '_t_' . $default_language ) )
			$p_type = $post_type_base;
		
		// Check post_type
		if ( $post_type == $p_type ) {
			continue;
		}
		
		// Ad to the result array the ID
		$result[$lang] = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='%s' AND meta_key='_translation_of' AND meta_value=%d ", array( $p_type, $post_id ) ) );
	}
	
	// Return the filled array
	return $result;
}

/**
 * Get the current language based on given post_id or global post
 * 
 * @param $post_id : the id of the post
 * @return the default language on failure and current on success
 * @author Rahe
 */
function mlf_get_current_language( $post_id = 0 ) {
	global $post;
	$thePost = $post;
	
	// Check given post_tid is good
	if( (int)$post_id <= 0 )
		$thePost = get_post( $post_id );
	
	// gEt default lang or option
	$options = get_option( MLF_OPTION_CONFIG );
	$defaultLang = $options['default_language'];
	
	// Explodde by prefix
	$pType = explode( '_t_', $thePost->post_type );
	
	// Check isset the ptype, its defualt if nothing
	if( !isset( $pType[1] ) )
		return $defaultLang;
	
	// Return detected lanaguage if detected
	if( isset( $pType[1] ) && in_array( $pType[1] , $options['enabled_languages'] ) )
		return $pType[1];
	
	// Return defaultLanguage if nothing founded
	return $defaultLang;
}

/**
 * Get all the registered translated post_types
 * 
 * @param void
 * @return array with all the slugs of post_types
 * @author Rahe
 */
function mlf_get_registered_post_types( $p_type = '' ) {
	// get the option
	$options = get_option( MLF_OPTION_CONFIG );
	
	// Get the array
	$out = array();
	
	// Check options filled
	if( !isset( $options['default_language'] ) || empty( $options['default_language'] ) || !isset( $options['enabled_languages'] ) || empty( $options['enabled_languages'] ) || !isset( $options['post_types'] ) || empty( $options['post_types'] ) )
		return $out;
	
	// Make all the registered post_types
	foreach( $options['post_types'] as $post_type ) {
		// If a post_type is given ,so skip the other post_types
		if( isset( $p_type ) && !empty( $p_type ) && $p_type != $post_type )
			continue;
		
		// Make all the languages
		foreach( $options['enabled_languages'] as $lang ) {
			// If we are on the defualt language, skip this one
			if( $lang == $options['default_language'] )
				continue;
			
			// Add the language
			$out[] = sanitize_key( $post_type.'_t_'.$lang );
		}
	}
	
	// Return the array filled
	return $out;
}
?>