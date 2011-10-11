<?php
class MLF_PostTypes {
	
	private $_config = array();
	private $_default = array();
	private $_options = array();
	
	function __construct() {
		
		// Get teh options
		$this->_config = get_option( MLF_OPTION_CONFIG );
		$this->_default = get_option( MLF_OPTION_DEFAULT );
		
		$this->_options = array_merge( $this->_config, $this->_default );
		
		$this->_options['enabled_languages'] = isset( $this->_options['enabled_languages'] )? $this->_options['enabled_languages'] : array() ;
		$this->_options['default_language'] = isset( $this->_options['default_language'] )? $this->_options['default_language'] : array() ;
		
		add_action( 'init', array( &$this ,'postTranslationsInit' ) );
		
		// Adds the Translation Column to the Edit screen
		
		add_filter( "manage_posts_columns", array( &$this ,'addColumn' ) );
		add_filter( "manage_pages_columns", array( &$this ,'addColumn' ) );
		
		add_action( "manage_posts_custom_column", array( &$this ,'addColumnContent' ) , 10, 2 );
		add_action( "manage_pages_custom_column", array( &$this ,'addColumnContent' ) , 10, 2 );
		
		add_action( 'admin_menu',  array( &$this ,'translationBox' ) );
		
		add_action( 'post_submitbox_misc_actions', array( &$this ,'mlf_copy_date_checkbox' ) );
		add_action( 'page_submitbox_misc_actions', array( &$this ,'mlf_copy_date_checkbox' ) );
	}
	
	function postTranslationsInit() {
		global $wp_post_types, $_wp_post_type_features;
		
		$language_name = $this->_options['language_name'];
		$enabled_languages = $this->_options['enabled_languages'];
		$default_language = $this->_options['default_language'];
		
		if( !empty( $enabled_languages ) ) {
			foreach ( $enabled_languages as $l ) {
				if ( $l == $default_language )
					continue;
				foreach ( $this->_options['post_types'] as $p_type ) {
					$labels = (array) $wp_post_types[$p_type]->labels;
					$labels['name'] .= ' - ' . $language_name[$l];
					$labels['menu_name'] .= ' - ' . $language_name[$l];
					
					// Post type position
					switch ( $p_type ) {
						case 'post':
							$menu_pos = 5;
							break;
						case 'page':
							$menu_pos = 20;
							break;
						default:
							$menu_pos = $wp_post_types[$p_type]->menu_position ? $wp_post_types[$p_type]->menu_position : 25;
					}
					
					// Get the current post_type features
					$p_type_supports = array_keys( $_wp_post_type_features[$p_type] );
					
					// Arguments for the post_type creation
					$args = array(
						'labels' => $labels,
						'public' => true,
						'rewrite' => array( 'slug' => $l),
						'capability_type' => $wp_post_types[$p_type]->capability_type,
						'hierarchical' => $wp_post_types[$p_type]->hierarchical == 1,
						'menu_position' => $menu_pos,
						'supports' => $p_type_supports
					);
					
					// Register the post type
					register_post_type( $p_type . '_t_' . $l, $args );
				}
			}
		}
		
		// Add columns for custom post types
		if( !empty( $this->_options['post_types'] ) ) {
			foreach (  $this->_options['post_types'] as $p_type ) {
				add_filter( 'manage_'.$p_type.'_posts_columns', array( &$this ,'addColumnContent' ), 10, 2 );
				add_action( 'save_'.$p_type, array( &$this , 'postSave' ) );
			}
		}
	}
	
	function addColumn( $defaults ) {
		global $pagenow;
		
		$enabled_languages = $this->_options['enabled_languages'];
		
		if( count( $enabled_languages ) <= 1 || get_query_var( 'post_status' ) == 'trash' ){
			return $defaults;
		}
		
		foreach( $defaults as $k => $v ) {
			$new_columns[$k] = $v;
			if( $k=='title' )
				$new_columns['post_translations'] = __( 'Translations', 'mlf' );
		}
		return $new_columns;
	}
	
	function translationBox() {
		$enabled_languages = isset( $this->_options['enabled_languages'] )? $this->_options['enabled_languages'] : array() ;
		$post_types = $this->_options['post_types'];
		
		foreach ( $post_types as $p ) {
			add_meta_box( 'post_translations',__( 'Post Translations', 'mlf' ), array( &$this, 'translationInnerBox' ), $p, 'side' );
			add_meta_box( 'mlf_other_version_id',__( 'Post Translations', 'mlf' ), array( &$this, 'otherVersions' ), $p, 'normal', 'high' );
			
			foreach ( $enabled_languages as $lang ) {
				add_meta_box( 'post_translations',__( 'Post Translations', 'mlf' ), array( &$this, 'translationInnerBox' ), $p . '_t_' . $lang, 'side' );
				add_meta_box( 'mlf_other_version_id',__( 'Post Translations', 'mlf' ), array( &$this, 'otherVersions' ), $p . '_t_' . $lang, 'normal', 'high' );
			}
		}
	}
	
	function addColumnContent( $column_name, $id = '' ) {
		
		// check right column
		if ( $column_name != "post_translations" )
			return $column_name;
		
		// Get enabled and default languages
		$enabled_languages = $this->_options['enabled_languages'];
		$default_language = $this->_options['default_language'];
		
		if( empty( $enabled_languages ) )
			return $column_name;
		
		// Get the post_type in 
		$post_type = get_query_var( 'post_type' );
		
		// Quick Edit
		if ( $post_type == '' && DOING_AJAX ) 
			$post_type = $_POST['post_type'];
		
		// Get the base post_type ( without special prefix)
		$post_type_base = preg_replace( '/^(\S+)_t_\S{2}$/', "$1", $post_type );
		
		echo '<ul class="languages-list">';
		foreach ( $enabled_languages as $lang ) {
			// Default value
			$translation_id = 0;
			
			// Get the 
			$p_type = $post_type_base . '_t_' . $lang;
			
			// Check not same
			if ( $p_type == $post_type_base . '_t_' . $default_language )
				$p_type = $post_type_base;
			
			// Check not same
			if ( $post_type == $p_type )
				continue;
			
			// Make the block with edit/add links
			$this->theTranslationBlock( $id, $p_type, $lang, '<li>', '</li>' );
			
		}
		echo '</ul>';
		
		return $column_name;
	}
	
	function translationInnerBox() {
		global $post,$wpdb;
		
		// Use nonce for verification
		wp_nonce_field( 'post_translation', '_wpTranslation_nonce' );
		
		if( !isset( $_GET['action'] ) && isset( $_GET['translation_of'] ) ) {
			$translation_of = $_GET['translation_of'];
			$id = $translation_of;
		} else {
			$translation_of = get_post_meta( $post->ID, '_translation_of', true );
			$id = $post->ID;
		}
		
		if( empty( $translation_of ) )
			return false;
		
		echo '<input type="hidden" name="_translation_of" value="' . esc_attr( $translation_of ). '" >';
		echo '<input type="hidden" name="_saveTranslation" value="saving" >';

		$default_language = $this->_options['default_language'];
		$enabled_languages = $this->_options['enabled_languages'];
	
		#só aparecer links pra criar ou editar traduções quando estiver editando posts
		if ( isset( $_GET['action'] ) && $_GET['action'] != 'edit' ) {
			_e( 'Save this post so you can add and edit translations', 'mlf' );
			return;
		}
		
		$post_type = $post->post_type;
		$post_type_base = preg_replace( '/^(\S+)_t_\S{2}$/', "$1", $post->post_type );
		
		echo '<ul>';
		foreach ( $enabled_languages as $lang ) {
			// Default value
			$translation_id = 0;
			
			// Get modified post_type
			$p_type = $post_type_base . '_t_' . $lang;
			
			// Check post_type base language
			if ($p_type == $post_type_base . '_t_' . $default_language)
				$p_type = $post_type_base;
			
			// If same ptype do not display the link edit/add
			if ( $post_type == $p_type )
				continue;
			
			// Make the block for the translation
			$this->theTranslationBlock( $id, $p_type, $lang, '<li>', '</li>', true );
			
		}
		echo '</ul>';
	}

	
	function theTranslationBlock( $tId = 0, $pType = '', $lang = '', $before = '', $after = '', $displayAdd = true ) {
		global $wpdb;
		
		if( !isset( $tId ) || (int)$tId <= 0 || !post_type_exists( $pType ) || !isset( $lang ) || empty( $lang ) )
			return false;

		// Make the edit/add Links
		if ( $translation_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='%s' AND meta_key='_translation_of' AND meta_value=%d", array( $pType, $tId ) ) ) ) {	
			echo $before.$this->theTranslationEditLink( $translation_id, $lang ).$after;
		} elseif( !isset( $translation_id ) && $displayAdd == true ) {
			echo $before.$this->theTranslationAddLink( $tId, $pType, $lang ).$after;
		}
	}

	function theTranslationEditLink( $tId, $lang ) {
		return mlf_translationEditLink( $tId, $lang );
	}
	
	function theTranslationAddLink( $tId, $pType, $lang ) {
		return mlf_translationAddLink( $tId, $pType, $lang );
	}
	/*
	function addRelationship( $original, $new ) {
		if ( !$original || !$new )
			return;
		
		$original_trans = get_post_meta( $original, '_translation_of' );
		$new_trans = get_post_meta( $new, '_translation_of' );
		
		$original_trans[] 	= $new;
		$new_trans[] 		= $original;
		
		update_post_meta( $original, '_translation_of', $original_trans );
		update_post_meta( $new, '_translation_of', $new_trans );
	
		$also_translation_of = get_post_meta( $original, '_translation_of' );
	
		if ( !empty( $also_translation_of ) && is_array( $also_translation_of ) ) {
			foreach ( $also_translation_of as $a ) {
				if ( $a != $new ) {
					update_post_meta( $new, '_translation_of', $a );
					update_post_meta( $a, '_translation_of', $new );
				}
			}
		} else {
			update_post_meta( $original, '_translation_of', array( $new ) );
		}
	}*/
	
	function add_post_meta($post_id, $meta_key, $meta_value) {
		global $wpdb;
		
		$meta_value = maybe_serialize( stripslashes_deep($meta_value) );
		
		if ( $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d AND meta_value = %s",
			$meta_key, $post_id, $meta_value ) ) )
			return false;
		
		add_post_meta($post_id, $meta_key, $meta_value);
		
	}
	
	function addRelationship($original, $new) {
	
		if (!$original || !$new)
			return;
		
		$this->add_post_meta($original, '_translation_of', $new);
		$this->add_post_meta($new, '_translation_of', $original);
		
		#var_dump($original, $new); die;
	
		$also_translation_of = get_post_meta($original, '_translation_of');
	
		if (is_array($also_translation_of)) {
			foreach ($also_translation_of as $a) {
				if ($a != $new) {
					$this->add_post_meta($new, '_translation_of', $a);
					$this->add_post_meta($a, '_translation_of', $new);
				}
			}
		}
		
	}

	function postSave( $post_id ) {
		global $mlf_avoid_recursive_save, $wpdb;
		
		if( !isset( $_POST['_translation_of'] ) || !isset( $_POST['_saveTranslation'] ) )
			return $post_id;
		
		// Check right part admin
		check_admin_referer( 'post_translation', '_wpTranslation_nonce' );
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
		
		$this->addRelationship( $_POST['_translation_of'], $post_id );
		
		if ( $_POST['mlf_copy_date'] == 1 && isset( $_POST['_translation_of'] ) && (int)$_POST['_translation_of'] > 0 ) {
			$from = get_post( $_POST['_translation_of'] );
			
			remove_action( 'save_'.$_POST['post_type'], array( &$this, 'postSave' ) );
			remove_action( 'save_post', array( &$this, 'postSave' ) );
				wp_update_post( array( 'ID' => $post_id, 'post_date' => $from->post_date, 'post_date_gmt' => $from->post_date_gmt ) );
			add_action( 'save_post', array( &$this, 'postSave' ) );
			add_action( 'save_'.$_POST['post_type'], array( &$this, 'postSave' ) );
		}
		
		return $post_id;
	}
	
	function mlf_copy_date_checkbox() {
		global $pagenow;
		if ( $pagenow == 'post-new.php' && isset( $_GET['translation_of'] ) &&  $from = $_GET['translation_of'] ) {
			echo '<br /><input type="checkbox" name="mlf_copy_date" value="1" checked>';
			_e( 'Copy the date of the original post when I first save this translation', 'mlf' );
		}
	}
	
	function otherVersions(){
		global $post;
	
		$default_language = $this->_options['default_language'];
		$class = '';
		
		$edit_post = $post;
	
		$posts = new WP_Query( 
			array(
				'post_type' => 'any',
				'meta_key' 	=> '_translation_of',
				'meta_value'	=> $post->ID,
			)
		);

		if ( $posts->have_posts() ) {
			$translation_version = array();
	
			while ( $posts->have_posts() ) {
				$posts->the_post();
				
				if ( preg_match( '/^\S+_t_(\S{2})$/', $post->post_type ) )
					$lang = preg_replace( '/^\S+_t_(\S{2})$/', "$1", $post->post_type );
				else
					$lang = $default_language;
					
				$translation_version[$lang] = '<h2>' . get_the_title() . '</h2>' . get_the_content();
			}
			$post = $edit_post;
		?>
			<div class="translation_div">
				<ul class="translation_tabs">
					<?php
						foreach ( $translation_version as $lang => $text ) {
							echo '<li class="' . $class . '"><a href="#post_translation_'. $lang . '">'.$this->_options['language_name'][$lang].'</a></li>';
						}
					 ?>
				</ul>
				<div class="post_translation_container">
					<?php
						foreach ( $translation_version as $lang => $text ) {
							echo '<div id="post_translation_' . $lang .'" class="translation_content"> ';
							echo apply_filters( 'the_content', $text );
							echo '</div>';
						}
					 ?>
				</div>
			</div> 
		<?php
	
		}
	
		$post = $edit_post;
	}
}