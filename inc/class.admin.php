<?php
class MLF_Admin {
	
	function __construct() {
		global $mlf_config;

		$mlf_config = array_merge( get_option( 'mlf_config' ), mlf_load_static_options() );
	
		// extract url information
		$mlf_config['url_info'] = mlf_extractURL($_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
		
		// check cookies for admin
		$mlf_config['current_language'] = $mlf_config['default_language'];
		
		$_SERVER['REQUEST_URI'] = $mlf_config['url_info']['url'];
		$_SERVER['HTTP_HOST'] = $mlf_config['url_info']['host'];
	
		$mlf_config['url_info']['url'] = mlf_convertURL(add_query_arg('lang',$mlf_config['default_language'],$mlf_config['url_info']['url']));
		
		add_action( 'admin_enqueue_scripts', array( &$this, 'addRessources' ) );
	}
	
	function addRessources( $hook = '' ) {
		if( $hook == 'settings_page_mlf' || $hook == 'post-new.php' || $hook == 'post.php'  ) {
			wp_enqueue_style('mlf-admin', MLF_PLUGIN_URL . 'css/style.css');
			wp_enqueue_script('mlf-admin', MLF_PLUGIN_URL . 'js/settings.js');	
		}
	}
}

?>