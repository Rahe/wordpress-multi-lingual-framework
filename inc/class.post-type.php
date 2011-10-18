<?php
class MLF_PostType extends MLF_PostTypes {
	private $_post_type = '';
	private $_lang = '';
	
	/**
	 * Construct the post_type
	 * 
	 * @param $post_type : the base post_type to add language
	 * @param $lang : the lang to add to the base post_type
	 * @return false o nfailure
	 * @author Rahe
	 */
	function __construct( $post_type = '', $lang = '' ) {
		// check post_type and lang given
		if( !isset( $post_type ) || empty( $post_type ) || !isset( $lang ) || empty( $lang ) )
			return false;
		
		// set class properties
		$this->_post_type = $post_type;
		$this->_lang = $lang;
		
		// Init options of the parent
		parent::initOptions();
		
		// Init the post_type
		$this->_init();
	}
	
	/**
	 * Init the post_types
	 * 
	 * @param $hook : the page hook for the current page loaded
	 * @return void
	 * @author Rahe
	 */
	private function _init() {
		// Get the registered post_types and features associated withthem
		global $wp_post_types,$_wp_post_type_features,$mlf;
		
		// Get the language name
		$language_name = $this->_options['language_name'];
		
		// Duplicate the labels, name and menu name of the current post_type
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
			'rewrite' => array( 'slug' => $this->_lang ),
			'capability_type' => $wp_post_types[$this->_post_type]->capability_type,
			'hierarchical' => $wp_post_types[$this->_post_type]->hierarchical == 1,
			'menu_position' => $menu_pos,
			'supports' => $p_type_supports
		);
		
		//Register the post_type
		register_post_type( $this->_post_type.'_t_'.$this->_lang, $args );
		
		// if admin add save and columns methods
		if( is_admin() ) {
			// Add the filters and actions
			add_filter( 'manage_'.$this->_post_type.'_posts_custom_column', array( $mlf['admin'],'addColumnContent' ), 10, 2 );
			add_action( 'save_post', array( $mlf['admin'], 'postSave' ) );
		}
	}
}