<?php
class MLF_Rewrite {
	function __construct() {
		// Parse the query
		add_action( 'parse_query', array( &$this, 'parseQuery' ), 9 );
	}
	
	function parseQuery( $query ) {
		
		// Check if admin
		if( is_admin() )
			return;
		
		if( !isset( $query->query_vars['post_type'] ) )
			return false;
		
		// Get the options
		$options = get_option( MLF_OPTION_CONFIG );
		
		// Get the lang
		$lang = explode( '_t_', $query->query_vars['post_type'] );
		
		// Check if not default and enabled
		if( !isset( $lang[1] ) || !in_array( $lang[1], $options['enabled_languages'] ) || $lang[1] == $options['default_language'] )
			return false;
		
		// Add the templates if this is an language
		add_action( 'template_redirect', array( &$this, 'templateRedirect' ) );
	}
	
	function templateRedirect( $templates = array() ) {
		global $wp_query;
		
		// Get post_type and language
		$els = explode( '_t_', $wp_query->query_vars['post_type'] );
		
		// Make the single templates
		$templates[] = 'single-'.$els[0].'-'.$els[1].'.php' ;
		$templates[] = 'single-'.$els[0].'.php' ;
		$templates[] = 'single.php';
		
		// Add the templates to the current
		locate_template( $templates, true );
		exit();
	}
}
?>