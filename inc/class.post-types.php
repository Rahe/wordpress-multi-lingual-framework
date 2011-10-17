<?php
class MLF_PostTypes {
	protected $_config = array();
	protected $_default = array();
	protected $_options = array();
	
	function __construct() {
		
		// init the options
		$this->initOptions();
		
		add_action( 'init', array( &$this ,'postTranslationsInit' ) );
	}
	
	function initOptions() {
		// Get the options
		$this->_config = get_option( MLF_OPTION_CONFIG );
		$this->_default = get_option( MLF_OPTION_DEFAULT );
		
		$this->_options = array_merge( $this->_config, $this->_default );
		
		$this->_options['enabled_languages'] = isset( $this->_options['enabled_languages'] )? $this->_options['enabled_languages'] : array() ;
		$this->_options['default_language'] = isset( $this->_options['default_language'] )? $this->_options['default_language'] : array() ;
		
	}
	
	function postTranslationsInit() {
		if( !empty( $this->_options['enabled_languages'] ) ) {
			foreach ( $this->_options['enabled_languages'] as $l ) {
				if ( $l == $this->_options['default_language'] )
					continue;
				foreach ( $this->_options['post_types'] as $p_type ) {	
					// Register the post type
					$pType = new MLF_PostType( $p_type, $l );
				}
			}
		}
	}
}