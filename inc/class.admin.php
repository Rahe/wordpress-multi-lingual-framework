<?php
class MLF_Admin extends MLF_PostTypes {
	
	function __construct() {
		
		// Get the options
		parent::initOptions();
		
		// Add the scripts if needed
		add_action( 'admin_enqueue_scripts', array( &$this, 'addRessources' ) );
		
		// Adds the Translation Column to the Edit screen
		add_filter( "manage_posts_columns", array( &$this ,'addColumn' ) );
		add_filter( "manage_pages_columns", array( &$this ,'addColumn' ) );
		
		// Add the content of the columns
		add_action( "manage_posts_custom_column", array( &$this ,'addColumnContent' ) , 10, 2 );
		add_action( "manage_pages_custom_column", array( &$this ,'addColumnContent' ) , 10, 2 );
		
		// Add the metaboxes
		add_action( 'admin_menu',  array( &$this ,'addTranslationBoxes' ) );
		
		// Add the checkboxes
		add_action( 'post_submitbox_misc_actions', array( &$this ,'duplicateOriginalDate' ) );
		add_action( 'page_submitbox_misc_actions', array( &$this ,'duplicateOriginalDate' ) );
	}
	
	/**
	 * Add the css/script on right pages
	 * 
	 * @param $hook : the page hook for the current page loaded
	 * @return void
	 * @author Rahe
	 */
	function addRessources( $hook = '' ) {
		// check if new_post or settings page
		if( $hook == 'settings_page_mlf' || $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'edit.php' ) {
			wp_enqueue_style( 'mlf-admin', MLF_PLUGIN_URL . 'ressources/css/style.css' );
			wp_enqueue_script( 'mlf-admin', MLF_PLUGIN_URL . 'ressources/js/settings.js' );	
		}
	}
	
	/**
	 * Register the columns for the enabled languages
	 * 
	 * @param $defaults : the current registered columns
	 * @return $new_columns|$default : the modified (or not) columns indexes
	 * @author Rahe
	 */
	function addColumn( $defaults ) {
		// If there is registered languages and not in trash
		if( count( $this->_options['enabled_languages'] ) <= 1 || get_query_var( 'post_status' ) == 'trash' ){
			return $defaults;
		}
			
		// put the translation column just after the title
		foreach( $defaults as $k => $v ) {
			$new_columns[$k] = $v;
			
			// if title push the column
			if( $k == 'title' )
				$new_columns['post_translations'] = __( 'Translations', 'mlf' );
		}
		// Return the columns reordened
		return $new_columns;
	}

	/**
	 * Add translation boxes for the post_types and the languages of the post_types
	 * 
	 * @param void
	 * @return void
	 * @author Rahe
	 */
	function addTranslationBoxes() {
		// Check there is registered post_types and languages active
		if( empty( $this->_options['post_types'] ) || empty( $this->_options['enabled_languages'] ) )
			return false;
		
		// For all the registered post_types, add basic metaboxes
		foreach ( $this->_options['post_types'] as $p ) {
			add_meta_box( 'post_translations',		__( 'Post Translations', 'mlf' ), 		array( &$this, 'translationInnerBox' ), 	$p, 'side' );
			add_meta_box( 'mlf_other_version_id',	__( 'Translations content', 'mlf' ), 	array( &$this, 'otherTranslationsTabs' ), 	$p, 'normal', 'high' );
			
			// Ad meta boxes for all the other languages and post_type translated
			foreach ( $this->_options['enabled_languages'] as $lang ) {
				add_meta_box( 'post_translations',		__( 'Post Translations', 'mlf' ), 		array( &$this, 'translationInnerBox' ), 	$p . '_t_' . $lang, 'side' );
				add_meta_box( 'mlf_other_version_id',	__( 'Translations content', 'mlf' ), 	array( &$this, 'otherTranslationsTabs' ), 	$p . '_t_' . $lang, 'normal', 'high' );
			}
		}
	}

	/**
	 * Add the translation column content
	 * 
	 * @param $column_name : the current column name
	 * @param $id : the id of the post
	 * @return $column_name : the current column name
	 * @author Rahe
	 */
	function addColumnContent( $column_name, $id = '' ) {
		// check right column
		if ( $column_name != "post_translations" )
			return $column_name;
		
		// Check there is languages enabled
		if( empty( $this->_options['enabled_languages'] ) )
			return $column_name;
		
		// Get the current post_type
		$post_type = get_query_var( 'post_type' );
		
		// Quick Edit
		if ( $post_type == '' && DOING_AJAX ) 
			$post_type = $_POST['post_type'];
		
		// Get the base post_type ( without special prefix)
		$post_type_base = preg_replace( '/^(\S+)_t_\S{2}$/', "$1", $post_type );
		
		// Display the language list
		echo '<ul class="languages-list">';
		foreach ( $this->_options['enabled_languages'] as $lang ) {
			// Default value
			$translation_id = 0;
			
			// Get the translated post_type
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
		
		// Retur the column name
		return $column_name;
	}

	/**
	 * Generate the translation box in right
	 * 
	 * @param void
	 * @return true|false
	 * @author Rahe
	 */
	function translationInnerBox() {
		// Get globals for the post and the wpdb
		global $post,$wpdb;
		
		// Use nonce for verification
		wp_nonce_field( 'post_translation', '_wpTranslation_nonce' );
		
		// Generate the translation_of id
		if( !isset( $_GET['action'] ) && isset( $_GET['translation_of'] ) ) {
			$translation_of = $_GET['translation_of'];
			$id = $translation_of;
		} else {
			// Get the translation id in metas
			$translation_of = get_post_meta( $post->ID, '_translation_of', true );
			$id = $post->ID;
		}
		
		// get the base post_type
		$post_type_base = preg_replace( '/^(\S+)_t_\S{2}$/', "$1", $post->post_type );
		
		// if no translation founded, so this is a new post, check if not basic post_type
		if( empty( $translation_of ) && !in_array( $post->post_type, $this->_options['post_types'] ) ) {
			// Get the ids not translated for this language
			$not__in = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='%s' AND meta_key='_translation_of' AND meta_value != ''", array( $post_type_base ) ) );
			
			// make the query with the not__in params
			$p_query = new WP_Query( array( 'post_type' => $post_type_base, 'post__not_in' => $not__in, 'posts_per_page' => 50 ) );
			
			// if no results, there is no elements to translate
			if( empty( $p_query->posts ) ) {
				_e( 'All your content is translated.', 'mlf' );
			} else {
				// Make the select and lbale basd on the query results
				echo '<label for="_translation">'.__( 'A translation of:', 'mlf' ).'</label>';
				echo '<select name="_translation_of" id="_translation" >';
				echo '<option value="0">'.__( 'None', 'mlf' ).'</option>';
				foreach( $p_query->posts as $q_post ){
					echo '<option value="'.esc_attr( $q_post->ID ).'">'.get_the_title( $q_post->ID ).'</option>';
				}
				echo '</select>';
				echo '<input type="hidden" name="_saveTranslation" value="saving" >';
			}
			
			return true;
		} else {
			// hidden fields for saving the elements
			echo '<input type="hidden" name="_translation_of" value="' . esc_attr( $translation_of ). '" >';
			echo '<input type="hidden" name="_saveTranslation" value="saving" >';
			
			// Display message to the user to save the post before allowing hil to add translations
			if ( isset( $_GET['action'] ) && $_GET['action'] != 'edit' ) {
				_e( 'Save this post so you can add and edit translations', 'mlf' );
				return false;
			}
			// Get thecurrent post_type
			$post_type = $post->post_type;
			
			// display the list for all tge langauges for add/edit translation
			echo '<ul>';
			foreach ( $this->_options['enabled_languages'] as $lang ) {
				// Default value
				$translation_id = 0;
				
				// Get modified post_type
				$p_type = $post_type_base . '_t_' . $lang;
				
				// Check post_type base language
				if ( $p_type == $post_type_base . '_t_' . $this->_options['default_language'] )
					$p_type = $post_type_base;
				
				// If same ptype do not display the link edit/add
				if ( $post_type == $p_type )
					continue;
				
				// Make the block for the translation
				$this->theTranslationBlock( $id, $p_type, $lang, '<li>', '</li>', true );
				
			}
			echo '</ul>';
		}
		return true;
	}

	/**
	 * Add a checkbox for duplicate the master post_date
	 * 
	 * @param void
	 * @return void
	 * @author Rahe
	 */
	function duplicateOriginalDate() {
		// Get current page global
		global $pagenow;
		
		// check if new post and translation of one post
		if ( $pagenow == 'post-new.php' && isset( $_GET['translation_of'] ) ) {
			echo '<br /><input type="checkbox" name="mlf_copy_date" id="mlf_copy_date" value="1" checked="checked" />';
			echo '<label for="mlf_copy_date">';
				_e( 'Copy the date of the original post when I first save this translation', 'mlf' );
			echo '</label>';
		}
	}
	
	/**
	 * Add a the metabox content for already translated elements on tabs
	 * 
	 * @param void
	 * @return void
	 * @author Rahe
	 */
	function otherTranslationsTabs() {
		// Get current post
		global $post;
		
		// init vars
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
		
		// if there is transaltions
		if ( $posts->have_posts() ) {
			$translation_version = array();
			
			while ( $posts->have_posts() ) {
				$posts->the_post();
				
				// If this is an translation, then get the lang from the post_type
				if ( strpos( $post->post_type, '_t_' ) !== false )
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
			// if not transaltions founded, user mesage
			_e( 'No translations yet.', 'mlf' );
		}
		
		// Reset post datas for the rest of the post
		wp_reset_postdata();
	}

	/**
	 * Save action for the post_type
	 * 
	 * @param $post_id : the post_id of the saved post
	 * @return $post_id : the post_id of the saved post
	 * @author Rahe
	 */
	function postSave( $post_id ) {
		// get global
		global $wpdb;
		
		// check there is an translation of given and save translation given
		if( !isset( $_POST['_translation_of'] ) || !isset( $_POST['_saveTranslation'] ) )
			return $post_id;
		
		// Check right part admin
		check_admin_referer( 'post_translation', '_wpTranslation_nonce' );
		
		// Check not autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
		
		// Add relationship between the current post and it translation
		$this->addRelationship( $_POST['_translation_of'], $post_id );
		
		// If we are copying the date of the master post, then update
		if ( isset( $_POST['mlf_copy_date'] ) && $_POST['mlf_copy_date'] == 1 && isset( $_POST['_translation_of'] ) && (int)$_POST['_translation_of'] > 0 ) {
			// Get the master post
			$from = get_post( $_POST['_translation_of'] );
			// Update the date and gmt_date
			$wpdb->update( $wpdb->posts, array( 'post_date' => $from->post_date , 'post_date_gmt' => $from->post_date_gmt ), array( 'ID' => $post_id ), array( '%s', '%s' ) );
		}
		// Retur nthe post_id
		return $post_id;
	}

	/**
	 * Generate the translation block
	 * 
	 * @param $tId : the id of the translated element
	 * @param $pType : pType of the translated element
	 * @param $lang : the lang of the pType to transalte
	 * @param $before : content to display before the list
	 * @param $after : content to display after the list
	 * @param $displayAdd : display Add button or not
	 * @return $post_id : the post_id of the saved post
	 * @author Rahe
	 */
	function theTranslationBlock( $tId = 0, $pType = '', $lang = '', $before = '', $after = '', $displayAdd = true ) {
		global $wpdb;
		
		// Check params
		if( !isset( $tId ) || (int)$tId <= 0 || !post_type_exists( $pType ) || !isset( $lang ) || empty( $lang ) )
			return false;

		// Make the edit/add Links
		if ( $translation_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='%s' AND meta_key='_translation_of' AND meta_value=%d", array( $pType, $tId ) ) ) ) {	
			echo $before.$this->theTranslationEditLink( $translation_id, $lang ).$after;
		} elseif( !isset( $translation_id ) && $displayAdd == true ) {
			echo $before.$this->theTranslationAddLink( $tId, $pType, $lang ).$after;
		}
	}
	
	/**
	 * Display the edit link
	 * 
	 * @param $tId : the id of the post translated
	 * @param $lang : the language to get
	 * @return the edit link|boolean on failure
	 * @author Rahe
	 */
	function theTranslationEditLink( $tId, $lang ) {
		return mlf_translationEditLink( $tId, $lang );
	}
	
	/**
	 * Display the add link
	 * 
	 * @param $tId : the id of the post translated
	 * @param $lang : the language to get
	 * @return the add link|boolean on failure
	 * @author Rahe
	 */
	function theTranslationAddLink( $tId, $pType, $lang ) {
		return mlf_translationAddLink( $tId, $pType, $lang );
	}
	
	/**
	 * Custom add post meta, check duplicate before adding
	 * 
	 * @param $post_id : the id of the post
	 * @param $meta_key : meta_key to use
	 * @param $meta_value : meta_value to insert
	 * @return the add link|boolean on failure
	 * @author Rahe
	 */
	function addPostMeta( $post_id, $meta_key, $meta_value ) {
		// lgobal for wpdb
		global $wpdb;
		
		// Check the meta_value, and sanitize it
		$meta_value = maybe_serialize( stripslashes_deep( $meta_value ) );
		
		// Check if duplicate or not
		if ( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d AND meta_value = %s", $meta_key, $post_id, $meta_value ) ) )
			return false;
		
		// Add post meta if needed
		add_post_meta($post_id, $meta_key, $meta_value);
	}
	
	/**
	 * Create a relationship between two translations or more
	 * 
	 * @param $orginial : original id of the element
	 * @param $new : id of the new element 
	 * @return boolean false on failure, true on success
	 * @author Rahe
	 */
	function addRelationship( $original, $new ) {
		// Check if not the same element
		if ( !$original || !$new )
			return false;
		
		// Add the post_meta between the original and the transalted element
		$this->addPostMeta( $original, '_translation_of', $new );
		$this->addPostMeta( $new, '_translation_of', $original );
	
		// check if there is more element to save or not
		$also_translation_of = get_post_meta( $original, '_translation_of' );
		
		// If this is an array, so add the link with all the other elements between them
		if ( is_array( $also_translation_of ) ) {
			foreach ( $also_translation_of as $a ) {
				// check not same elements
				if ( $a == $new )
					continue;
				
				$this->addPostMeta( $new, '_translation_of', $a );
				$this->addPostMeta( $a, '_translation_of', $new );
			}
		}
		return true;
	}
}
?>