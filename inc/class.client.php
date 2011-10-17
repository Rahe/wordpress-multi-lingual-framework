<?php
class MLF_Client{
	function __construct() {
		global $mlf;

		// Init the post_types
		$mlf['post-types'] = new MLF_PostTypes();

		// init rewriting
		$mlf['rewrite'] = new MLF_Rewrite();
	}
}
?>