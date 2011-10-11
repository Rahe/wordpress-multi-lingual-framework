<?php
class MLF_Admin {
	
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'addRessources' ) );
	}
	
	function addRessources( $hook = '' ) {
		if( $hook == 'settings_page_mlf' || $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'edit.php' ) {
			wp_enqueue_style('mlf-admin', MLF_PLUGIN_URL . 'ressources/css/style.css');
			wp_enqueue_script('mlf-admin', MLF_PLUGIN_URL . 'ressources/js/settings.js');	
		}
	}
}

?>