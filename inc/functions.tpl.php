<?php
function mlf_links_to_languages() {
	global $post;
	
	$mlf_config = get_option( MLF_OPTION_CONFIG );
	$mlf_default = get_option( MLF_OPTION_DEFAULT );
	
	$mlf_config = array_merge( $mlf_config, $mlf_default );
	
	$originalLanguage = mlf_get_current_language( $post->ID );

	echo '<ul id="languages_list">';
	if ( is_singular() ) {
		global $wp_query,$post;
		
		$other_languages = mlf_get_tranlsations_ids( $post->ID, $post->post_type );
		
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
	
	
	} else {
		$other_languages = mlf_get_tranlsations_ids( $post->ID, $post->post_type );
		foreach ( $mlf_config['enabled_languages'] as $lang ) {
				
			if ( $originalLanguage == $lang )
				continue;
			
			if ( isset( $other_languages[$lang] ) ) {
				$link = get_permalink( $other_languages[$lang] );
			}else{
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

	echo '</ul>';
}

function mlf_isEnabled( $lang ) {
	$options = get_option( MLF_OPTION_CONFIG );
	
	return ( isset( $options['enabled_languages'] ) && in_array( $lang, $options['enabled_languages'] ) );
}

add_filter( 'the_content', 'mlf_add_link_to_other_languages' );
function mlf_add_link_to_other_languages( $content ) {
	if( is_admin() )
		return $content;
	
	global $post;
	$r = '';
	$other_languages = mlf_get_tranlsations_ids( $post->ID, $post->post_type );
	
	$mlf_config = get_option( MLF_OPTION_CONFIG );
	$mlf_default = get_option( MLF_OPTION_DEFAULT );
	
	$mlf_config = array_merge( $mlf_config, $mlf_default );
	
	// We have to temporarily change the language to retrieve unflitered permalinks
	$currentLanguage = isset( $mlf_config['current_language'] )? $mlf_config['current_language'] : '' ;
	$mlf_config['current_language'] = $mlf_config['default_language'];
	
	$r .= '<ul id="postmeta_translations">';
	
	if( empty( $other_languages ) )
		return $content;
	
	foreach( $other_languages as $lang => $l ) {
		
		$label = $mlf_config['labels']['available'][$lang] ? $mlf_config['labels']['available'][$lang] : sprintf(__('This entry is also available in %s','mlf'), $mlf_config['language_name'][$lang]);
		if ( $l ) {
			$r .= "<li><a href='" . get_permalink( $l ) . "'>" . $label . "</a></li>";
		}
	}
	$r .= '</ul>';
	
	return $content . $r;
}

function mlf_add_not_available_message( $content ) {
	global $post, $mlf_config;
	
	$message = $mlf_config['labels']['not_available'][$mlf_config['current_language']] ? $mlf_config['labels']['not_available'][$mlf_config['current_language']] : sprintf(__('This entry is not available in %s','mlf'), $mlf_config['language_name'][$mlf_config['current_language']]);
	
	return "<p class='mlf_alert'>$message</p>$content";
}

?>