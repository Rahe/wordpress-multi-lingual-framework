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
	$mlf_default['language_name']['fi'] = "suomi";
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

	// Flag images configuration
	// Look in /flags/ directory for a huge list of flags for usage
	$mlf_default['flag']['en'] = 'gb.png';
	$mlf_default['flag']['de'] = 'de.png';
	$mlf_default['flag']['zh'] = 'cn.png';
	$mlf_default['flag']['fi'] = 'fi.png';
	$mlf_default['flag']['fr'] = 'fr.png';
	$mlf_default['flag']['nl'] = 'nl.png';
	$mlf_default['flag']['se'] = 'se.png';
	$mlf_default['flag']['it'] = 'it.png';
	$mlf_default['flag']['ro'] = 'ro.png';
	$mlf_default['flag']['hu'] = 'hu.png';
	$mlf_default['flag']['ja'] = 'jp.png';
	$mlf_default['flag']['es'] = 'es.png';
	$mlf_default['flag']['vi'] = 'vn.png';
	$mlf_default['flag']['ar'] = 'arle.png';
	$mlf_default['flag']['pt'] = 'br.png';

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

	// Location of flags (needs trailing slash!)
	$mlf_default['flag_location'] =  "/flags/";
	
	// enable the use of following languages (order=>language)
	$mlf_config['enabled_languages'] = array(
		'0' => 'pt',
		'1' => 'es', 
		'2' => 'en'
	);
	
	// Add defualt options
	$mlf_config['hide_default_language'] = true;
	
	$mlf_config['default_language'] =  "en";
	
	$mlf_config['url_mode'] =  "path";
	
	$mlf_config['post_types'] =  array('post');
	
	update_option( MLF_OPTION_CONFIG, $mlf_config );
	update_option( MLF_OPTION_DEFAULT, $mlf_default );
}

function mlf_deactivate() {
	global $wpdb;
	
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'mlf_%'" );
}

function mlf_translationEditLink( $tId, $lang ) {
	$languages = get_option( MLF_OPTION_DEFAULT );
	
	if( !isset( $languages['language_name'][$lang] ) || !isset( $tId ) || (int)$tId <= 0 )
		return false;
	
	return '<a title="Edit" href="'.get_edit_post_link( $tId ).'"><span class="icon_edit"><span>Edit</span></span> '.$languages['language_name'][$lang].'</a></li>';
}

function mlf_translationAddLink( $tId, $pType, $lang ) {
	$languages = get_option( MLF_OPTION_DEFAULT );
	
	if( !isset( $languages['language_name'][$lang] ) || !isset( $tId ) || (int)$tId <= 0 || !post_type_exists( $pType ) )
		return false;
	
	if( !isset( $languages['language_name'][$lang] ) )
		return false;
	
	return '<a title="Add" href="'.admin_url( 'post-new.php?post_type='.$pType.'&translation_of='.$tId ).'"><span class="icon_add"><span>Add</span> </span> '.$languages['language_name'][$lang].'</a>';
}
?>