<?php

function mlf_page_admin() { 
    global $wp_post_types;
    
    $mlf_config = get_option('mlf_config');

    $mlf_static = mlf_load_static_options();
    
    ?>
    <div class="wrap">
		
		
			<form method="post" action="options.php">
			
			
				<?php settings_fields('multi-language-settings-group'); ?>
				
                <h2><?php _e('Multi Language Options', 'mlf'); ?></h2>
                
				<h3><?php _e('Default language', 'mlf'); ?></h3>
				
                    <select name="mlf_config[default_language]">
                    <?php foreach( $mlf_config['enabled_languages'] as $lang ) : ?>
                    
                        <option value="<?php echo $lang; ?>" <?php if ($lang == $mlf_config['default_language']) echo 'selected'; ?> > <?php echo $mlf_static['language_name'][$lang]; ?></option>
                    
                    <?php endforeach; ?>
                    </select>
                    
                <h3><?php _e('URL Mode', 'mlf'); ?></h3>
                
                    <input type="radio" name="mlf_config[url_mode]" value="subdomain" <?php if ('subdomain' == $mlf_config['url_mode']) echo 'checked'; ?> > <?php _e('subdomain - es.mysite.com', 'mlf'); ?>  UNTESTED<br />
                    <input type="radio" name="mlf_config[url_mode]" value="path" <?php if ('path' == $mlf_config['url_mode']) echo 'checked'; ?> > <?php _e('subdirectory - mysite.com/es', 'mlf'); ?> <br />
                    <input type="radio" name="mlf_config[url_mode]" value="querystring" <?php if ('querystring' == $mlf_config['url_mode']) echo 'checked'; ?> > <?php _e('query string - mysite.com/?lang=es', 'mlf'); ?> UNTESTED<br />
                
                
                <h3><?php _e('Post types that will be translated', 'mlf'); ?></h3>
                
                    <?php foreach ($wp_post_types as $type_name => $type) : ?>
                        
                        <?php if ($type_name == 'attachment' || $type_name == 'revision' || $type_name == 'nav_menu_item' || preg_match('/_t_/', $type_name)) continue; ?>
                    
                        <input type="checkbox" name="mlf_config[post_types][]" value="<?php echo $type_name; ?>" <?php if (in_array($type_name, $mlf_config['post_types'])) echo 'checked'; ?> > <?php echo $type_name; ?> <br />
                    
                    <?php endforeach; ?>
                
                
                <h3><?php _e('Languages', 'mlf'); ?></h3>
                    <table>
                        <tr>
                            <td>Enabled</td>
                            <td>Language</td>
                            <td>Not Available message</td>
                            <td>Translation available link label</td>
                        </tr>
                    
                    <?php foreach ($mlf_static['language_name'] as $lang => $name): ?>
                    
                        <tr>
                            <td>
                                <input type="checkbox" name="mlf_config[enabled_languages][]" value="<?php echo $lang; ?>" <?php if (in_array($lang, $mlf_config['enabled_languages'])) echo 'checked'; ?> >
                            </td>
                            <td><?php echo $name; ?></td>
                            <td><input type="text" name="mlf_config[labels][not_available][<?php echo $lang; ?>]" value="<?php echo htmlspecialchars($mlf_config['labels']['not_available'][$lang]); ?>"></td>
                            <td><input type="text" name="mlf_config[labels][available][<?php echo $lang; ?>]" value="<?php echo htmlspecialchars($mlf_config['labels']['available'][$lang]); ?>"></td>
                        </tr>
                    
                    <?php endforeach; ?>
                    </table>
                    
                <input type="hidden" name="mlf_config[hide_default_language]" value="1">
                
				<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			
			
			</form>
		
		
		</div>
        
        <?php
    
}

?>
