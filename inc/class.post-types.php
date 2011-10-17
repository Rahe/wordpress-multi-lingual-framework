<?php
class MLF_PostTypes {
	protected $_config = array();
	protected $_default = array();
	protected $_options = array();
	
	function __construct() {
		
		// init the options
		$this->initOptions();
		
		// Add init post_types
		add_action( 'init', array( &$this ,'postTranslationsInit' ) );
	}
	
	/**
	 * Get the different options and mertge them
	 * 
	 * @param void
	 * @return void
	 * @author Rahe
	 */
	function initOptions() {
		// Get the options
		$this->_config = get_option( MLF_OPTION_CONFIG );
		$this->_default = get_option( MLF_OPTION_DEFAULT );
		
		// Merge config and default
		$this->_options = array_merge( $this->_config, $this->_default );
		
		// Check the enabled and default
		$this->_options['enabled_languages'] = isset( $this->_options['enabled_languages'] )? $this->_options['enabled_languages'] : array() ;
		$this->_options['default_language'] = isset( $this->_options['default_language'] )? $this->_options['default_language'] : array() ;
		
	}
	
	/**
	 * Make the post_types for languages
	 * 
	 * @param void
	 * @return void
	 * @author Rahe
	 */
	function postTranslationsInit() {
		// check there is post_types and one enabled language
		if( !empty( $this->_options['enabled_languages'] ) && !empty( $this->_options['post_types'] ) ) {
			// For every language add the translation
			foreach ( $this->_options['enabled_languages'] as $l ) {
				// Do not add for the default language language
				if ( $l == $this->_options['default_language'] )
					continue;
				// For all the post_type used, add the different post_type translated
				foreach ( $this->_options['post_types'] as $p_type ) {	
					// Register the post type
					$pType = new MLF_PostType( $p_type, $l );
				}
			}
		}
	}
}