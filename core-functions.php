<?php 

function mlf_updateGettextDatabases($force = false, $only_for_language = '') {
    global $mlf_config;

    if(!is_dir(WP_LANG_DIR)) {
        if(!@mkdir(WP_LANG_DIR)){                    
            return false;
        }
    }
    $next_update = get_option('mlf_next_update_mo');
  
    if(time() < $next_update && !$force) 
        return true;
        
    update_option('mlf_next_update_mo', time() + 7*24*60*60);
    
    foreach($mlf_config['locale'] as $lang => $locale) {
        
        if(mlf_isEnabled($only_for_language) && $lang != $only_for_language) continue;
        
        if(!mlf_isEnabled($lang)) continue;
        
        if($locale == 'en_US') continue;
        
        if($ll = @fopen(trailingslashit(WP_LANG_DIR).$locale.'.mo.filepart','a')) {
            // can access .mo file
            fclose($ll);
            // try to find a .mo file
            if(!$lcr = @fopen('http://svn.automattic.com/wordpress-i18n/'.$locale.'/tags/'.$GLOBALS['wp_version'].'/messages/'.$locale.'.mo','r'))
            if(!$lcr = @fopen('http://svn.automattic.com/wordpress-i18n/'.substr($locale,0,2).'/tags/'.$GLOBALS['wp_version'].'/messages/'.$locale.'.mo','r'))
            if(!$lcr = @fopen('http://svn.automattic.com/wordpress-i18n/'.$locale.'/branches/'.$GLOBALS['wp_version'].'/messages/'.$locale.'.mo','r'))
            if(!$lcr = @fopen('http://svn.automattic.com/wordpress-i18n/'.substr($locale,0,2).'/branches/'.$GLOBALS['wp_version'].'/messages/'.$locale.'.mo','r'))
            if(!$lcr = @fopen('http://svn.automattic.com/wordpress-i18n/'.$locale.'/branches/'.$GLOBALS['wp_version'].'/'.$locale.'.mo','r'))
            if(!$lcr = @fopen('http://svn.automattic.com/wordpress-i18n/'.substr($locale,0,2).'/branches/'.$GLOBALS['wp_version'].'/'.$locale.'.mo','r'))
            if(!$lcr = @fopen('http://svn.automattic.com/wordpress-i18n/'.$locale.'/trunk/messages/'.$locale.'.mo','r')) 
            if(!$lcr = @fopen('http://svn.automattic.com/wordpress-i18n/'.substr($locale,0,2).'/trunk/messages/'.$locale.'.mo','r')) {
            // couldn't find a .mo file
            if(filesize(trailingslashit(WP_LANG_DIR).$locale.'.mo.filepart')==0) unlink(trailingslashit(WP_LANG_DIR).$locale.'.mo.filepart');
                continue;
            }
            
            // found a .mo file, update local .mo
            $ll = fopen(trailingslashit(WP_LANG_DIR).$locale.'.mo.filepart','w');
            while(!feof($lcr)) {
                // try to get some more time
                @set_time_limit(30);
                $lc = fread($lcr, 8192);
                fwrite($ll,$lc);
            }
            fclose($lcr);
            fclose($ll);
            // only use completely download .mo files
            rename(trailingslashit(WP_LANG_DIR).$locale.'.mo.filepart',trailingslashit(WP_LANG_DIR).$locale.'.mo');
        }
    }
    return true;
}

function qtrans_header(){
	global $mlf_config;
	echo "\n<meta http-equiv=\"Content-Language\" content=\"".$mlf_config['locale'][$mlf_config['language']]."\" />\n";

	if(is_404()) return;
	// set links to translations of current page
	foreach($mlf_config['enabled_languages'] as $language) {
		if($language != qtrans_getLanguage())
			echo '<link hreflang="'.$language.'" href="'.qtrans_convertURL('',$language).'" rel="alternate" rev="alternate" />'."\n";
	}	
}


?>
