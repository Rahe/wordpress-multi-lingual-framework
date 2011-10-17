<?php	
/**
 * Display the links to the languages
 * 
 * @param void
 * @return false
 * @author Rahe
 */
function mlf_links_to_languages() {
	global $post,$wp_query;
	
	// Get the options
	$mlf_config = get_option( MLF_OPTION_CONFIG );
	$mlf_default = get_option( MLF_OPTION_DEFAULT );
	
	// Merge the options
	$mlf_config = array_merge( $mlf_config, $mlf_default );
	
	// Get the language of the curent post
	$originalLanguage = mlf_get_current_language( $post->ID );

	// Get the other languages
	$other_languages = mlf_get_tranlsations_ids( $post->ID, $post->post_type );
	
	// Language list
	echo '<ul id="languages_list">';
	if ( is_singular() ) {
		// Ge the other languages

		
		if( isset( $mlf_config['enabled_languages'] ) && !empty( $mlf_config['enabled_languages'] ) ) {
			foreach ( $mlf_config['enabled_languages'] as $lang ) {
				if ( $originalLanguage == $lang )
					continue;
				
				// does this entry have a translation?
				if ( isset( $other_languages[$lang] ) ) {
					// We have to temporarily change the language to retrieve unflitered permalinks
					$link = get_permalink( $other_languages[$lang] );
				} else {
					continue;
				}
	
				?>
				<li>
					<a href="<?php echo $link; ?>">
						<?php echo $mlf_config['language_name'][$lang]; ?>
					</a>
				</li>
				<?php
				
			}
		}
	} else {
		if( isset( $mlf_config['enabled_languages'] ) && !empty( $mlf_config['enabled_languages'] ) ) {
			foreach ( $mlf_config['enabled_languages'] as $lang ) {
				
				// Continue on current language
				if ( $originalLanguage == $lang )
					continue;
				
				// If the ise other languages get the link
				if ( isset( $other_languages[$lang] ) ) {
					$link = get_permalink( $other_languages[$lang] );
				} else {
					continue;
				}
				?>
				<li>
					<a href="<?php echo esc_url( $link ); ?>">
						<?php echo $mlf_config['language_name'][$lang]; ?>
					</a>
				</li>
				<?php
			}
		}
	}

	echo '</ul>';
}

/**
 * Check if translations are activated for the given language
 * 
 * @param $lang : the language to test
 * @return false|true on success
 * @author Rahe
 */
function mlf_isEnabled( $lang ) {
	// Get options
	$options = get_option( MLF_OPTION_CONFIG );
	
	// Check on array
	return ( isset( $options['enabled_languages'] ) && in_array( $lang, $options['enabled_languages'] ) );
}

add_filter( 'the_content', 'mlf_add_link_to_other_languages' );
/**
 * Add the links to the content
 * 
 * @param $content : the content before filter
 * @return $content : the content after filter
 * @author Rahe
 */
function mlf_add_link_to_other_languages( $content ) {
	global $post;
	
	// check on admin
	if( is_admin() )
		return $content;
	
	// init vars
	$r = '';
	// get th other lanaguges
	$other_languages = mlf_get_tranlsations_ids( $post->ID, $post->post_type );
	
	// Get the options
	$mlf_config = get_option( MLF_OPTION_CONFIG );
	$mlf_default = get_option( MLF_OPTION_DEFAULT );
	
	// merge the options
	$mlf_config = array_merge( $mlf_config, $mlf_default );
	
	// We have to temporarily change the language to retrieve unflitered permalinks
	$currentLanguage = isset( $mlf_config['current_language'] )? $mlf_config['current_language'] : '' ;
	$mlf_config['current_language'] = $mlf_config['default_language'];
	
	// if no other languages, reutnr the content
	if( empty( $other_languages ) )
		return $content;
	
		
	// make the return
	$r .= '<ul id="postmeta_translations">';
	
	foreach( $other_languages as $lang => $l ) {
		
		$label = $mlf_config['labels']['available'][$lang] ? $mlf_config['labels']['available'][$lang] : sprintf(__('This entry is also available in %s','mlf'), $mlf_config['language_name'][$lang]);
		if ( $l ) {
			$r .= "<li><a href='" . get_permalink( $l ) . "'>" . $label . "</a></li>";
		}
	}
	$r .= '</ul>';
	
	// Return the languages at the end of the content
	return $content . $r;
}

//TODO :
	/* Make this part work */
/**
 * Add the not available message of the content if given
 * 
 * @param $content : the content before filter
 * @return $content : the content after filter
 * @author Rahe
 */
function mlf_add_not_available_message( $content ) {
	global $post, $mlf_config;
	
	$message = $mlf_config['labels']['not_available'][$mlf_config['current_language']] ? $mlf_config['labels']['not_available'][$mlf_config['current_language']] : sprintf(__('This entry is not available in %s','mlf'), $mlf_config['language_name'][$mlf_config['current_language']]);
	
	return '<p class="mlf_alert">'.esc_html( $message ).'</p>'.$content;
}
?>