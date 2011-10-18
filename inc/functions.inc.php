<?php
function mlf_activate() {
	// Add specific role
	$admin = get_role('administrator');
	$admin->add_cap('manage-multi-language-framework');
	
	
	// Add locale slugs
	$mlf_default['locale']['de'] = "de_DE";
	$mlf_default['locale']['en'] = "en_US";
	$mlf_default['locale']['zh'] = "zh_CN";
	$mlf_default['locale']['fi'] = "fi";
	$mlf_default['locale']['fr'] = "fr_FR";
	$mlf_default['locale']['nl'] = "nl_NL";
	$mlf_default['locale']['se'] = "sv_SE";
	$mlf_default['locale']['it'] = "it_IT";
	$mlf_default['locale']['ro'] = "ro_RO";
	$mlf_default['locale']['hu'] = "hu_HU";
	$mlf_default['locale']['ja'] = "ja";
	$mlf_default['locale']['es'] = "es_ES";
	$mlf_default['locale']['vi'] = "vi";
	$mlf_default['locale']['ar'] = "ar";
	$mlf_default['locale']['pt'] = "pt_BR";

	// Names for languages in the corresponding language, add more if needed
	$mlf_default['language_name']['de'] = "Deutsch";
	$mlf_default['language_name']['en'] = "English";
	$mlf_default['language_name']['zh'] = "中文";
	$mlf_default['language_name']['fi'] = "Suomi";
	$mlf_default['language_name']['fr'] = "Français";
	$mlf_default['language_name']['nl'] = "Nederlands";
	$mlf_default['language_name']['se'] = "Svenska";
	$mlf_default['language_name']['it'] = "Italiano";
	$mlf_default['language_name']['ro'] = "Română";
	$mlf_default['language_name']['hu'] = "Magyar";
	$mlf_default['language_name']['ja'] = "日本語";
	$mlf_default['language_name']['es'] = "Español";
	$mlf_default['language_name']['vi'] = "Tiếng Việt";
	$mlf_default['language_name']['ar'] = "العربية";
	$mlf_default['language_name']['pt'] = "Português";

	// Full country names as locales for Windows systems
	$mlf_default['windows_locale']['aa'] = "Afar";
	$mlf_default['windows_locale']['ab'] = "Abkhazian";
	$mlf_default['windows_locale']['ae'] = "Avestan";
	$mlf_default['windows_locale']['af'] = "Afrikaans";
	$mlf_default['windows_locale']['am'] = "Amharic";
	$mlf_default['windows_locale']['ar'] = "Arabic";
	$mlf_default['windows_locale']['as'] = "Assamese";
	$mlf_default['windows_locale']['ay'] = "Aymara";
	$mlf_default['windows_locale']['az'] = "Azerbaijani";
	$mlf_default['windows_locale']['ba'] = "Bashkir";
	$mlf_default['windows_locale']['be'] = "Belarusian";
	$mlf_default['windows_locale']['bg'] = "Bulgarian";
	$mlf_default['windows_locale']['bh'] = "Bihari";
	$mlf_default['windows_locale']['bi'] = "Bislama";
	$mlf_default['windows_locale']['bn'] = "Bengali";
	$mlf_default['windows_locale']['bo'] = "Tibetan";
	$mlf_default['windows_locale']['br'] = "Breton";
	$mlf_default['windows_locale']['bs'] = "Bosnian";
	$mlf_default['windows_locale']['ca'] = "Catalan";
	$mlf_default['windows_locale']['ce'] = "Chechen";
	$mlf_default['windows_locale']['ch'] = "Chamorro";
	$mlf_default['windows_locale']['co'] = "Corsican";
	$mlf_default['windows_locale']['cs'] = "Czech";
	$mlf_default['windows_locale']['cu'] = "Church Slavic";
	$mlf_default['windows_locale']['cv'] = "Chuvash";
	$mlf_default['windows_locale']['cy'] = "Welsh";
	$mlf_default['windows_locale']['da'] = "Danish";
	$mlf_default['windows_locale']['de'] = "German";
	$mlf_default['windows_locale']['dz'] = "Dzongkha";
	$mlf_default['windows_locale']['el'] = "Greek";
	$mlf_default['windows_locale']['en'] = "English";
	$mlf_default['windows_locale']['eo'] = "Esperanto";
	$mlf_default['windows_locale']['es'] = "Spanish";
	$mlf_default['windows_locale']['et'] = "Estonian";
	$mlf_default['windows_locale']['eu'] = "Basque";
	$mlf_default['windows_locale']['fa'] = "Persian";
	$mlf_default['windows_locale']['fi'] = "Finnish";
	$mlf_default['windows_locale']['fj'] = "Fijian";
	$mlf_default['windows_locale']['fo'] = "Faeroese";
	$mlf_default['windows_locale']['fr'] = "French";
	$mlf_default['windows_locale']['fy'] = "Frisian";
	$mlf_default['windows_locale']['ga'] = "Irish";
	$mlf_default['windows_locale']['gd'] = "Gaelic (Scots)";
	$mlf_default['windows_locale']['gl'] = "Gallegan";
	$mlf_default['windows_locale']['gn'] = "Guarani";
	$mlf_default['windows_locale']['gu'] = "Gujarati";
	$mlf_default['windows_locale']['gv'] = "Manx";
	$mlf_default['windows_locale']['ha'] = "Hausa";
	$mlf_default['windows_locale']['he'] = "Hebrew";
	$mlf_default['windows_locale']['hi'] = "Hindi";
	$mlf_default['windows_locale']['ho'] = "Hiri Motu";
	$mlf_default['windows_locale']['hr'] = "Croatian";
	$mlf_default['windows_locale']['hu'] = "Hungarian";
	$mlf_default['windows_locale']['hy'] = "Armenian";
	$mlf_default['windows_locale']['hz'] = "Herero";
	$mlf_default['windows_locale']['ia'] = "Interlingua";
	$mlf_default['windows_locale']['id'] = "Indonesian";
	$mlf_default['windows_locale']['ie'] = "Interlingue";
	$mlf_default['windows_locale']['ik'] = "Inupiaq";
	$mlf_default['windows_locale']['is'] = "Icelandic";
	$mlf_default['windows_locale']['it'] = "Italian";
	$mlf_default['windows_locale']['iu'] = "Inuktitut";
	$mlf_default['windows_locale']['ja'] = "Japanese";
	$mlf_default['windows_locale']['jw'] = "Javanese";
	$mlf_default['windows_locale']['ka'] = "Georgian";
	$mlf_default['windows_locale']['ki'] = "Kikuyu";
	$mlf_default['windows_locale']['kj'] = "Kuanyama";
	$mlf_default['windows_locale']['kk'] = "Kazakh";
	$mlf_default['windows_locale']['kl'] = "Kalaallisut";
	$mlf_default['windows_locale']['km'] = "Khmer";
	$mlf_default['windows_locale']['kn'] = "Kannada";
	$mlf_default['windows_locale']['ko'] = "Korean";
	$mlf_default['windows_locale']['ks'] = "Kashmiri";
	$mlf_default['windows_locale']['ku'] = "Kurdish";
	$mlf_default['windows_locale']['kv'] = "Komi";
	$mlf_default['windows_locale']['kw'] = "Cornish";
	$mlf_default['windows_locale']['ky'] = "Kirghiz";
	$mlf_default['windows_locale']['la'] = "Latin";
	$mlf_default['windows_locale']['lb'] = "Letzeburgesch";
	$mlf_default['windows_locale']['ln'] = "Lingala";
	$mlf_default['windows_locale']['lo'] = "Lao";
	$mlf_default['windows_locale']['lt'] = "Lithuanian";
	$mlf_default['windows_locale']['lv'] = "Latvian";
	$mlf_default['windows_locale']['mg'] = "Malagasy";
	$mlf_default['windows_locale']['mh'] = "Marshall";
	$mlf_default['windows_locale']['mi'] = "Maori";
	$mlf_default['windows_locale']['mk'] = "Macedonian";
	$mlf_default['windows_locale']['ml'] = "Malayalam";
	$mlf_default['windows_locale']['mn'] = "Mongolian";
	$mlf_default['windows_locale']['mo'] = "Moldavian";
	$mlf_default['windows_locale']['mr'] = "Marathi";
	$mlf_default['windows_locale']['ms'] = "Malay";
	$mlf_default['windows_locale']['mt'] = "Maltese";
	$mlf_default['windows_locale']['my'] = "Burmese";
	$mlf_default['windows_locale']['na'] = "Nauru";
	$mlf_default['windows_locale']['nb'] = "Norwegian Bokmal";
	$mlf_default['windows_locale']['nd'] = "Ndebele, North";
	$mlf_default['windows_locale']['ne'] = "Nepali";
	$mlf_default['windows_locale']['ng'] = "Ndonga";
	$mlf_default['windows_locale']['nl'] = "Dutch";
	$mlf_default['windows_locale']['nn'] = "Norwegian Nynorsk";
	$mlf_default['windows_locale']['no'] = "Norwegian";
	$mlf_default['windows_locale']['nr'] = "Ndebele, South";
	$mlf_default['windows_locale']['nv'] = "Navajo";
	$mlf_default['windows_locale']['ny'] = "Chichewa; Nyanja";
	$mlf_default['windows_locale']['oc'] = "Occitan (post 1500)";
	$mlf_default['windows_locale']['om'] = "Oromo";
	$mlf_default['windows_locale']['or'] = "Oriya";
	$mlf_default['windows_locale']['os'] = "Ossetian; Ossetic";
	$mlf_default['windows_locale']['pa'] = "Panjabi";
	$mlf_default['windows_locale']['pi'] = "Pali";
	$mlf_default['windows_locale']['pl'] = "Polish";
	$mlf_default['windows_locale']['ps'] = "Pushto";
	$mlf_default['windows_locale']['pt'] = "Portuguese";
	$mlf_default['windows_locale']['qu'] = "Quechua";
	$mlf_default['windows_locale']['rm'] = "Rhaeto-Romance";
	$mlf_default['windows_locale']['rn'] = "Rundi";
	$mlf_default['windows_locale']['ro'] = "Romanian";
	$mlf_default['windows_locale']['ru'] = "Russian";
	$mlf_default['windows_locale']['rw'] = "Kinyarwanda";
	$mlf_default['windows_locale']['sa'] = "Sanskrit";
	$mlf_default['windows_locale']['sc'] = "Sardinian";
	$mlf_default['windows_locale']['sd'] = "Sindhi";
	$mlf_default['windows_locale']['se'] = "Sami";
	$mlf_default['windows_locale']['sg'] = "Sango";
	$mlf_default['windows_locale']['si'] = "Sinhalese";
	$mlf_default['windows_locale']['sk'] = "Slovak";
	$mlf_default['windows_locale']['sl'] = "Slovenian";
	$mlf_default['windows_locale']['sm'] = "Samoan";
	$mlf_default['windows_locale']['sn'] = "Shona";
	$mlf_default['windows_locale']['so'] = "Somali";
	$mlf_default['windows_locale']['sq'] = "Albanian";
	$mlf_default['windows_locale']['sr'] = "Serbian";
	$mlf_default['windows_locale']['ss'] = "Swati";
	$mlf_default['windows_locale']['st'] = "Sotho";
	$mlf_default['windows_locale']['su'] = "Sundanese";
	$mlf_default['windows_locale']['sv'] = "Swedish";
	$mlf_default['windows_locale']['sw'] = "Swahili";
	$mlf_default['windows_locale']['ta'] = "Tamil";
	$mlf_default['windows_locale']['te'] = "Telugu";
	$mlf_default['windows_locale']['tg'] = "Tajik";
	$mlf_default['windows_locale']['th'] = "Thai";
	$mlf_default['windows_locale']['ti'] = "Tigrinya";
	$mlf_default['windows_locale']['tk'] = "Turkmen";
	$mlf_default['windows_locale']['tl'] = "Tagalog";
	$mlf_default['windows_locale']['tn'] = "Tswana";
	$mlf_default['windows_locale']['to'] = "Tonga";
	$mlf_default['windows_locale']['tr'] = "Turkish";
	$mlf_default['windows_locale']['ts'] = "Tsonga";
	$mlf_default['windows_locale']['tt'] = "Tatar";
	$mlf_default['windows_locale']['tw'] = "Twi";
	$mlf_default['windows_locale']['ug'] = "Uighur";
	$mlf_default['windows_locale']['uk'] = "Ukrainian";
	$mlf_default['windows_locale']['ur'] = "Urdu";
	$mlf_default['windows_locale']['uz'] = "Uzbek";
	$mlf_default['windows_locale']['vi'] = "Vietnamese";
	$mlf_default['windows_locale']['vo'] = "Volapuk";
	$mlf_default['windows_locale']['wo'] = "Wolof";
	$mlf_default['windows_locale']['xh'] = "Xhosa";
	$mlf_default['windows_locale']['yi'] = "Yiddish";
	$mlf_default['windows_locale']['yo'] = "Yoruba";
	$mlf_default['windows_locale']['za'] = "Zhuang";
	$mlf_default['windows_locale']['zh'] = "Chinese";
	$mlf_default['windows_locale']['zu'] = "Zulu";
	
	// enable the use of following languages (order=>language)
	$mlf_config['enabled_languages'] = array(
		'0' => 'en',
		'1' => 'es', 
		'2' => 'pt'
	);
	
	// Add defualt options
	$mlf_config['hide_default_language'] = true;
	
	// Defualt language
	$mlf_config['default_language'] =  "en";
	
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
	if( !isset( $languages['language_name'][$lang] ) || !isset( $tId ) || (int)$tId <= 0 || !post_type_exists( $pType ) )
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
	$post_type_base = preg_replace( '/(.+)_t_([a-zA-Z]{2})/', "$1", $post_type );
	
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
		$p_type = $post_type_base . '_t_' . $lang;
		
		// Check if defualt language
		if ($p_type == $post_type_base . '_t_' . $default_language)
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
function mlf_get_registered_post_types() {
	// get the option
	$options = get_option( MLF_OPTION_CONFIG );
	
	// Get the array
	$out = array();
	
	// Check options filled
	if( !isset( $options['default_language'] ) || empty( $options['default_language'] ) || !isset( $options['enabled_languages'] ) || empty( $options['enabled_languages'] ) || !isset( $options['post_types'] ) || empty( $options['post_types'] ) )
		return $out;
	
	// Make all the registered post_types
	foreach( $options['post_types'] as $post_type ) {
		// Make all the languages
		foreach( $options['enabled_languages'] as $lang ) {
			// If we are on the defualt language, skip this one
			if( $lang == $options['default_language'] )
				continue;
			
			// Add the language
			$out[] = $post_type.'_t_'.$lang;
		}
	}
	
	// Return the array filled
	return $out;
}
?>