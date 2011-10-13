<?php
class MLF_Rewrite {
	function __construct() {
		// Generating for the datas
		add_action( 'generate_rewrite_rules', array( &$this, 'rewriteRules' ) );
		
		// add the query vars
		add_filter( 'query_vars', array( &$this, 'addQueryVar' ) );
		
		// Parse the query
		add_action( 'parse_query', array( &$this, 'parseQuery' ) );
	}

	function parseQuery( $query ) {
	}

	function rewriteRules( $wp_rewrite ) {
		// Get the options
		$options = get_option( MLF_OPTION_CONFIG );

		// Get languages and the post_types
		$enabled_languages = isset( $options['enabled_languages'] )? $options['enabled_languages'] : '' ;
		$post_types = isset( $options['post_types'] )? $options['post_types'] : '' ;
		
		// check if languages and post_types
		if( empty( $enabled_languages ) || empty( $post_types ) )
			return false;

		// implode the languages
		$langs = implode( $enabled_languages, '|' );

		// Make the rewrite rules
		foreach( $post_types as $ptype ) {
			$new_rules[ '('.$langs.')/[^/]+/'] = 'index.php?mlf_lang='.$wp_rewrite->preg_index( 1 ).'&post_type='.$ptype.'_t_'.$wp_rewrite->preg_index( 1 );
		}
		
		// Put them on the 
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
	
	function addQueryVar( $query_vars = array() ) {
		$query_vars[] = 'mlf_lang';
		return $query_vars;
	}
	
	function templateRedirect() {
		
	}
}
?>