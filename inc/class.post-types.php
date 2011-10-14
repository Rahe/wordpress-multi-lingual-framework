<?php
class MLF_PostTypes {
	
	protected $_config = array();
	protected $_default = array();
	protected $_options = array();
	
	function __construct() {
		
		// init the options
		$this->initOptions();
		
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
	
	function addColumn( $defaults ) {
		global $pagenow;
		
		if( count( $this->_options['enabled_languages'] ) <= 1 || get_query_var( 'post_status' ) == 'trash' ){
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
		foreach ( $this->_options['post_types'] as $p ) {
			add_meta_box( 'post_translations',__( 'Post Translations', 'mlf' ), array( &$this, 'translationInnerBox' ), $p, 'side' );
			add_meta_box( 'mlf_other_version_id',__( 'Post Translations', 'mlf' ), array( &$this, 'otherVersions' ), $p, 'normal', 'high' );
			
			foreach ( $this->_options['enabled_languages'] as $lang ) {
				add_meta_box( 'post_translations',__( 'Post Translations', 'mlf' ), array( &$this, 'translationInnerBox' ), $p . '_t_' . $lang, 'side' );
				add_meta_box( 'mlf_other_version_id',__( 'Post Translations', 'mlf' ), array( &$this, 'otherVersions' ), $p . '_t_' . $lang, 'normal', 'high' );
			}
		}
	}
	
	function addColumnContent( $column_name, $id = '' ) {
		
		// check right column
		if ( $column_name != "post_translations" )
			return $column_name;
		
		if( empty( $this->_options['enabled_languages'] ) )
			return $column_name;
		
		// Get the post_type in 
		$post_type = get_query_var( 'post_type' );
		
		// Quick Edit
		if ( $post_type == '' && DOING_AJAX ) 
			$post_type = $_POST['post_type'];
		
		// Get the base post_type ( without special prefix)
		$post_type_base = preg_replace( '/^(\S+)_t_\S{2}$/', "$1", $post_type );
		
		echo '<ul class="languages-list">';
		foreach ( $this->_options['enabled_languages'] as $lang ) {
			// Default value
			$translation_id = 0;
			
			// Get the 
			$p_type = $post_type_base . '_t_' . $lang;
			
			// Check not same
			if ( $p_type == $post_type_base . '_t_' . $this->_options['default_language'] )
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
	
		#só aparecer links pra criar ou editar traduções quando estiver editando posts
		if ( isset( $_GET['action'] ) && $_GET['action'] != 'edit' ) {
			_e( 'Save this post so you can add and edit translations', 'mlf' );
			return;
		}
		
		$post_type = $post->post_type;
		$post_type_base = preg_replace( '/^(\S+)_t_\S{2}$/', "$1", $post->post_type );
		
		echo '<ul>';
		foreach ( $this->_options['enabled_languages'] as $lang ) {
			// Default value
			$translation_id = 0;
			
			// Get modified post_type
			$p_type = $post_type_base . '_t_' . $lang;
			
			// Check post_type base language
			if ($p_type == $post_type_base . '_t_' . $this->_options['default_language'])
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
	
	function add_post_meta($post_id, $meta_key, $meta_value) {
		global $wpdb;
		
		$meta_value = maybe_serialize( stripslashes_deep($meta_value) );
		
		if ( $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d AND meta_value = %s",
			$meta_key, $post_id, $meta_value ) ) )
			return false;
		
		add_post_meta($post_id, $meta_key, $meta_value);
		
	}
	
	function addRelationship( $original, $new ) {
	
		if (!$original || !$new)
			return;
		
		$this->add_post_meta( $original, '_translation_of', $new );
		$this->add_post_meta( $new, '_translation_of', $original );
		
		#var_dump($original, $new); die;
	
		$also_translation_of = get_post_meta( $original, '_translation_of' );
	
		if ( is_array( $also_translation_of ) ) {
			foreach ( $also_translation_of as $a ) {
				if ( $a != $new ) {
					$this->add_post_meta( $new, '_translation_of', $a );
					$this->add_post_meta( $a, '_translation_of', $new );
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
		
		if ( isset( $_POST['mlf_copy_date'] ) && $_POST['mlf_copy_date'] == 1 && isset( $_POST['_translation_of'] ) && (int)$_POST['_translation_of'] > 0 ) {
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
		
		$class = '';
		$original = $post->ID;
		
		// Get the post datas
		$posts = new WP_Query( 
			array(
				'post_type' => 'any',
				'post__not_in ' => array( $post->ID ),
				'meta_query' => array(
					array(
						'key' => '_translation_of',
						'value' => $post->ID,
						'compare' => '='
					)
				)
			)
		);
		
		if ( $posts->have_posts() ) {
			$translation_version = array();
	
			while ( $posts->have_posts() ) {
				$posts->the_post();
				
				// If this is an traduction, then get the lang from the post_type
				if ( preg_match( '/^\S+_t_(\S{2})$/', $post->post_type ) )
					$lang = preg_replace( '/^\S+_t_(\S{2})$/', "$1", $post->post_type );
				else
					$lang = $this->_options['default_language'];
				
				// If we are on the same post as current, do not diplay the content
				if( $original == get_the_ID() )
					continue;
				
				// Make title and content
				$translation_version[$lang] = '<h2>' . get_the_title() . '</h2>' . get_the_content();
			}
			
			// If translation available, display tabs
			if( !empty( $translation_version ) ) :
			?>
			<div class="translation_div">
				<ul class="translation_tabs">
					<?php
						foreach ( $translation_version as $lang => $text ) {
							// Get the language name
							$title = $this->_options['language_name'][$lang];
							
							// if original langague,display it
							if( $lang == $this->_options['default_language'] )
								$title .= __( ' - Original', 'mlf' );
							
							echo '<li class="' .esc_attr( $class ). '"><a href="#post_translation_'. esc_attr( $lang ) . '">'.$title.'</a></li>';
						}
					 ?>
				</ul>
				<div class="post_translation_container">
					<?php
						foreach ( $translation_version as $lang => $text ) {
							echo '<div id="post_translation_' .esc_attr( $lang ).'" class="translation_content"> ';
							echo apply_filters( 'the_content', $text );
							echo '</div>';
						}
					 ?>
				</div>
			</div>
			<?php
			endif;
		} else {
			_e( 'No translations yet.', 'mlf' );
		}
		
		// Reset post datas for the rest of the post
		wp_reset_postdata();
	}
}