<?php
class MLF_PostType extends MLF_PostTypes {
	private $_post_type = '';
	private $_lang = '';
	
	function __construct( $post_type = '', $lang = '' ) {
		
		if( !isset( $post_type ) || empty( $post_type ) || !isset( $lang ) || empty( $lang ) )
			return false;
		
		$this->_post_type = $post_type;
		$this->_lang = $lang;
		
		parent::initOptions();
		
		$this->init();
	}
	
	function init() {
		global $wp_post_types,$_wp_post_type_features;
		
		$language_name = $this->_options['language_name'];
		
		$labels = (array) $wp_post_types[$this->_post_type]->labels;
		$labels['name'] .= ' - ' . $language_name[$this->_lang];
		$labels['menu_name'] .= ' - ' . $language_name[$this->_lang];
		
		// Post type position
		switch ( $this->_post_type ) {
			case 'post':
				$menu_pos = 5;
				break;
			case 'page':
				$menu_pos = 20;
				break;
			default:
				$menu_pos = $wp_post_types[$this->_post_type]->menu_position ? $wp_post_types[$this->_post_type]->menu_position : 25;
		}
		
		// Get the current post_type features
		$p_type_supports = array_keys( $_wp_post_type_features[$this->_post_type] );
		
		// Arguments for the post_type creation
		$args = array(
			'labels' => $labels,
			'public' => true,
			'rewrite' => array( 'slug' => $this->_lang),
			'capability_type' => $wp_post_types[$this->_post_type]->capability_type,
			'hierarchical' => $wp_post_types[$this->_post_type]->hierarchical == 1,
			'menu_position' => $menu_pos,
			'supports' => $p_type_supports
		);
		//Register the post_type
		register_post_type( $this->_post_type.'_t_'.$this->_lang, $args );
		
		// Add the fileters
		add_filter( 'manage_'.$this->_post_type.'_posts_columns', array( &$this ,'addColumnContent' ), 10, 2 );
		add_action( 'save_'.$this->_post_type, array( &$this , 'postSave' ) );
	}
}