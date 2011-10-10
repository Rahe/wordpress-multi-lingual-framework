<?php
class MLF_PostTypes {

	function __construct() {
		global $mlf_avoid_recursive_save;
		$mlf_avoid_recursive_save = false;
		
		add_action( 'init', array( &$this ,'postTranslationsInit' ) );
		
		// Adds the Translation Column to the Edit screen
		
		add_filter( "manage_posts_columns", array( &$this ,'addColumn' ) );
		
		add_action( "manage_posts_custom_column", array( &$this ,'addColumnContent' ) , 10, 2 );
		add_action( "manage_pages_custom_column", array( &$this ,'addColumnContent' ) , 10, 2 );
		
		add_action( 'admin_menu',  array( &$this ,'translationBox' ) );
		
		add_action( 'post_submitbox_misc_actions', array( &$this ,'mlf_copy_date_checkbox' ) );
		add_action( 'page_submitbox_misc_actions', array( &$this ,'mlf_copy_date_checkbox' ) );
	}
	
	function postTranslationsInit() {
		global $wp_post_types, $mlf_config, $_wp_post_type_features;
		
		$language_name = $mlf_config['language_name'];
		$enabled_languages = $mlf_config['enabled_languages'];
		$default_language = $mlf_config['default_language'];
		if( !empty( $enabled_languages ) ) {
			foreach ( $enabled_languages as $l ) {
				if ( $l == $default_language )
					continue;
				foreach ( $mlf_config['post_types'] as $p_type ) {
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
		foreach ( $mlf_config['post_types'] as $p_type ) {
			add_filter( "manage_{$p_type}_posts_columns", array( &$this ,'addColumnContent' ), 9, 2 );
			add_action( "save_$p_type", array( &$this , 'postSave' ) );
		}
	}
	
	function addColumn( $defaults ) {
		global $pagenow;
		
		$enabled_languages = mlf_get_option( 'enabled_languages' );
		
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
	
	function addColumnContent( $column_name, $id = '' ) {
		global $wpdb;
		
		if ( $column_name != "post_translations" )
			return false;
		
		$enabled_languages = mlf_get_option( 'enabled_languages' );
		$default_language = mlf_get_option( 'default_language' );
		
		$flag_location = mlf_get_option( 'flag_location' );
		$post_type = get_query_var( 'post_type' );
		$flag = mlf_get_option( 'flag' );
		
		// Quick Edit
		if ( $post_type == '' && DOING_AJAX ) 
			$post_type = $_POST['post_type'];
		
		$post_type_base = preg_replace( '/^(\S+)_t_\S{2}$/', "$1", $post_type );
		
		foreach ( $enabled_languages as $lang ) {
			
			$translation_id = false;
			$p_type = $post_type_base . '_t_' . $lang;
			$flag_img = MLF_PLUGIN_URL.'ressources' . $flag_location . $flag[$lang];
			
			if ( $p_type == $post_type_base . '_t_' . $default_language )
				$p_type = $post_type_base;

			if ( $post_type == $p_type )
				continue;

			if ( $translation_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='%s' AND meta_key='_translation_of' AND meta_value=%d", array( $p_type, $id ) ) ) ) {	
				echo "<a title='Edit' href='".get_edit_post_link( $translation_id )."'><span class='icon_edit'><span>edit</span></span> <img src='$flag_img'/></a> ";
			} else {
				echo "<a title='Add' href='post-new.php?post_type=$p_type&translation_of=$id'><span class='icon_add'><span>add</span> </span> <img src='$flag_img'/></a> ";
			}
		}
	}
	
	function translationBox() {
		$enabled_languages = mlf_get_option( 'enabled_languages' );
		$post_types = mlf_get_option( 'post_types' );
		
		foreach ( $post_types as $p ) {
		
			add_meta_box( 'post_translations',__( 'Post Translations', 'mlf' ), array( &$this, 'translationInnerBox' ), $p, 'side' );
			add_meta_box( 'mlf_other_version_id',__( 'Post Translations', 'mlf' ), array( &$this, 'otherVersions' ), $p, 'normal', 'high' );
			
			foreach ($enabled_languages as $lang) {
				add_meta_box( 'post_translations',__( 'Post Translations', 'mlf' ), array( &$this, 'translationInnerBox' ), $p . '_t_' . $lang, 'side' );
				add_meta_box( 'mlf_other_version_id',__( 'Post Translations', 'mlf' ), array( &$this, 'otherVersions' ), $p . '_t_' . $lang, 'normal', 'high' );
			}
		}
	}
	
	function translationInnerBox() {
		global $post,$wpdb;

		// Use nonce for verification
		echo '<input type="hidden" name="post_translation_noncename" id="emissoras_noncename" value="' . 
		  wp_create_nonce( 'post_translation_noncename' ) . '" />';
	
		if( isset( $_GET['action'] ) && $_GET['action'] != 'edit' ) {
			$translation_of = $_GET['translation_of'];
		} else {
			$translation_of = get_post_meta( $post->ID, '_translation_of', true );
		}
		
		echo '<input type="hidden" name="_translation_of" value="' . $translation_of . '" >';

		$default_language = mlf_get_option( 'default_language' );
		$enabled_languages = mlf_get_option( 'enabled_languages' );
	
		#só aparecer links pra criar ou editar traduções quando estiver editando posts
		if ( isset( $_GET['action'] ) && $_GET['action'] != 'edit' ) {
			_e( 'Save this post so you can add and edit translations', 'mlf' );
			return;
		}
		
		$post_type = $post->post_type;
		$post_type_base = preg_replace( '/^(\S+)_t_\S{2}$/', "$1", $post->post_type );
		
		$flag_location = mlf_get_option( 'flag_location' );
		$flag = mlf_get_option( 'flag' );
		
		foreach ( $enabled_languages as $lang ) {
			
			$translation_id = false;
			$p_type = $post_type_base . '_t_' . $lang;			
			$flag_img = MLF_PLUGIN_URL. 'ressources' . $flag_location . $flag[$lang];
			
			if ($p_type == $post_type_base . '_t_' . $default_language)
				$p_type = $post_type_base;
	
			if ( $post_type == $p_type )
				continue;
			
			#echo "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='$p_type' AND meta_key='_translation_of' AND meta_value={$post->ID} ";
			
			if ( $translation_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts p JOIN $wpdb->postmeta pm ON post_id = p.ID WHERE post_status <> 'trash' AND post_type='%s' AND meta_key='_translation_of' AND meta_value=%d", array( $p_type, $post->ID ) ) ) ) {	
				echo "<a title='Edit' href='".get_edit_post_link( $translation_id )."'><span class='icon_edit'><span>edit</span></span> <img src='$flag_img'/></a> ";
			} else {
				echo "<a title='Add' href='post-new.php?post_type=$p_type&translation_of={$post->ID}'><span class='icon_add'><span>add</span> </span> <img src='$flag_img'/></a> ";
			}
		}
	}
	
	function addRelationship( $original, $new ) {
		if ( !$original || !$new )
			return;

		update_post_meta( $original, '_translation_of', $new );
		update_post_meta( $new, '_translation_of', $original );
		
		#var_dump($original, $new); die;
	
		$also_translation_of = get_post_meta( $original, '_translation_of' );
	
		if ( is_array( $also_translation_of ) ) {
			foreach ( $also_translation_of as $a ) {
				if ( $a != $new ) {
					update_post_meta( $new, '_translation_of', $a );
					update_post_meta( $a, '_translation_of', $new );
				}
			}
		}
	}

	function postSave( $post_id ) {
		global $mlf_avoid_recursive_save, $wpdb;
		
		if( !isset( $_GET['translation_of'] ) )
			return $post_id;
		
		if ( !wp_verify_nonce( $_POST['post_translation_noncename'], 'post_translation_noncename' ) ) {
			return $post_id;
		}
	
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
		
		$this->addRelationship( $_POST['_translation_of'], $post_id );
		
		if ( $_POST['mlf_copy_date'] == 1 && $_POST['_translation_of'] && $mlf_avoid_recursive_save === false ) {
			$mlf_avoid_recursive_save = true;
			$from = get_post( $_POST['_translation_of'], 'ARRAY_A' );

			#$wpdb->update($wpdb->posts, $to, $to['ID']);
			mysql_query("UPDATE $wpdb->posts SET post_date = '" . $from['post_date'] . "', post_date_gmt = '" . $from['post_date_gmt'] . "' WHERE ID = " . $post_id);

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
	
		$default_language = mlf_get_option( 'default_language' );
		$flag_location = mlf_get_option( 'flag_location' );
		$flag = mlf_get_option( 'flag' );
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
							$flag_img = MLF_PLUGIN_URL.'ressources' . $flag_location . $flag[$lang];
							echo '<li class="' . $class . '"><a href="#post_translation_'. $lang . '"><img src="' . $flag_img . '"></a></li>';
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