<?php
class MLF_Client{
	
	function __construct() {
		add_action( 'widgets_init', array( &$this, 'registerWidget' ) );
	}
	
	function registerWidget() {
		register_widget("WidgetMultiLanguageFramework");
	}
}
?>