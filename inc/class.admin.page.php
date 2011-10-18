<?php
class MLF_Admin_Page{
	function __construct() {
		add_action( 'admin_menu', array( &$this ,'adminMenu' ) );
		
		//call register settings function
		add_action( 'admin_init', array( &$this, 'registerSettings' ) );
	}

	function adminMenu() {
		add_submenu_page( 
			'options-general.php', 
			__( 'Multi Language Settings', 'mlf' ), 
			__( 'Multi Language', 'mlf' ), 
			'manage_options', 
			'mlf', 
			array( &$this, 'adminPage' )
		);
	}

	function registerSettings(){
		register_setting( 'multi-language-settings-group', 'mlf_config' );
	}

	function adminPage() {
		$mlf_config = get_option( MLF_OPTION_CONFIG );
		$mlf_default = get_option( MLF_OPTION_DEFAULT );
		
		$mlf_config['enabled_languages'] = isset( $mlf_config['enabled_languages'] ) && !empty( $mlf_config['enabled_languages'] )? $mlf_config['enabled_languages'] : array() ;
		$mlf_config['default_language'] = isset( $mlf_config['default_language'] )? $mlf_config['default_language'] : '' ;
		$mlf_config['post_types'] = isset( $mlf_config['post_types'] )? $mlf_config['post_types'] : array() ;
		?>
		<div class="wrap">
			<form method="post" action="options.php">
				<?php settings_fields( 'multi-language-settings-group' ); ?>
				<h2><?php screen_icon( 'options-general' ); _e( 'Multi Language Options', 'mlf' ); ?></h2>
				<h3><?php _e( 'Default language', 'mlf' ); ?></h3>
					<select name="mlf_config[default_language]">
					<?php foreach( $mlf_config['enabled_languages'] as $lang ) : ?>
						<option value="<?php esc_attr_e( $lang ); ?>" <?php selected( $lang, $mlf_config['default_language'] ); ?> > <?php echo $mlf_default['language_name'][$lang]; ?></option>
					<?php endforeach; ?>
					</select>
				<h3><?php _e( 'URL Mode', 'mlf' ); ?></h3>
					<input type="radio" name="mlf_config[url_mode]" value="subdomain" <?php if ( 'subdomain' == $mlf_config['url_mode']) echo 'checked'; ?> > <?php _e( 'subdomain - es.mysite.com', 'mlf' ); ?>  UNTESTED<br />
					<input type="radio" name="mlf_config[url_mode]" value="path" <?php if ( 'path' == $mlf_config['url_mode']) echo 'checked'; ?> > <?php _e( 'subdirectory - mysite.com/es', 'mlf' ); ?> <br />
					<input type="radio" name="mlf_config[url_mode]" value="querystring" <?php if ( 'querystring' == $mlf_config['url_mode']) echo 'checked'; ?> > <?php _e( 'query string - mysite.com/?lang=es', 'mlf' ); ?> UNTESTED<br />
				
				<h3><?php _e( 'Post types that will be translated', 'mlf' ); ?></h3>
					<div>
					<?php foreach ( get_post_types( array( 'show_ui' => true ), 'objects' ) as $name => $ptype ) :
						// Check not already translated post_type
						if ( preg_match( '/_t_/', $name ) ) 
							continue; 
					?>
						<input type="checkbox" name="mlf_config[post_types][]" value="<?php esc_attr_e( $name ); ?>" <?php checked( in_array( $name, $mlf_config['post_types'] ), true ); ?> > <?php echo $ptype->label; ?> <br />
					</div>
					<?php endforeach; ?>
				<h3><?php _e( 'Languages', 'mlf' ); ?></h3>
					<table class="wp-list-table widefat posts">
						<thead>
							<tr>
								<th><?php _e( 'Enabled', 'mlf' ); ?></th>
								<th><?php _e( 'Language', 'mlf' ); ?></th>
								<th><?php _e( 'Not Available message', 'mlf' ); ?></th>
								<th><?php _e( 'Translation available link label', 'mlf' ); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php _e( 'Enabled', 'mlf' ); ?></th>
								<th><?php _e( 'Language', 'mlf' ); ?></th>
								<th><?php _e( 'Not Available message', 'mlf' ); ?></th>
								<th><?php _e( 'Translation available link label', 'mlf' ); ?></th>
							</tr>
						</tfoot>
					<?php foreach( $mlf_default['language_name'] as $lang => $name ): 
						$not_avaible = isset( $mlf_default['labels']['not_available'][$lang] )? $mlf_default['labels']['not_available'][$lang] : '' ;
						$avaible = isset( $mlf_default['labels']['available'][$lang] )? $mlf_default['labels']['available'][$lang] : '' ;
					?>
						<tr>
							<td>
								<input type="checkbox" name="mlf_config[enabled_languages][]" value="<?php echo $lang; ?>" <?php checked( in_array( $lang, $mlf_config['enabled_languages'] ), true ); ?> >
							</td>
							<td><?php echo $name; ?></td>
							<td><input type="text" name="mlf_config[labels][not_available][<?php esc_attr_e( $lang ); ?>]" value="<?php esc_html_e( $not_avaible ); ?>"></td>
							<td><input type="text" name="mlf_config[labels][available][<?php esc_attr_e( $lang ); ?>]" value="<?php esc_html_e( $avaible ); ?>"></td>
						</tr>
					<?php endforeach; ?>
					</table>
				<input type="hidden" name="mlf_config[hide_default_language]" value="1">
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" />
				</p>
			</form>
		</div>
	<?php
	}
}
?>