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

// returns cleaned string and language information
function mlf_extractURL($url, $host = '', $referer = '') {
    global $mlf_config;
    $home = mlf_parseURL(get_option('home'));
    $home['path'] = trailingslashit($home['path']);
    $referer = mlf_parseURL($referer);
    
    $result = array();
    $result['language'] = $mlf_config['default_language'];
    $result['url'] = $url;
    $result['original_url'] = $url;
    $result['host'] = $host;
    $result['redirect'] = false;
    $result['internal_referer'] = false;
    $result['home'] = $home['path'];
    
    switch($mlf_config['url_mode']) {
        case QT_URL_PATH:
            // pre url
            $url = substr($url, strlen($home['path']));
            if($url) {
                // might have language information
                if(preg_match("#^([a-z]{2})(/.*)?$#i",$url,$match)) {
                    if(mlf_isEnabled($match[1])) {
                        // found language information
                        $result['language'] = $match[1];
                        $result['url'] = $home['path'].substr($url, 3);
                    }
                }
            }
            break;
        case QT_URL_DOMAIN:
            // pre domain
            if($host) {
                if(preg_match("#^([a-z]{2}).#i",$host,$match)) {
                    if(mlf_isEnabled($match[1])) {
                        // found language information
                        $result['language'] = $match[1];
                        $result['host'] = substr($host, 3);
                    }
                }
            }
            break;
    }
    
    // check if referer is internal
    if($referer['host']==$result['host'] && mlf_startsWith($referer['path'], $home['path'])) {
        // user coming from internal link
        $result['internal_referer'] = true;
    }
    
    if(isset($_GET['lang']) && mlf_isEnabled($_GET['lang'])) {
        // language override given
        $result['language'] = $_GET['lang'];
        $result['url'] = preg_replace("#(&|\?)lang=".$result['language']."&?#i","$1",$result['url']);
        $result['url'] = preg_replace("#[\?\&]+$#i","",$result['url']);
    } elseif($home['host'] == $result['host'] && $home['path'] == $result['url']) {
        if(empty($referer['host'])||!$mlf_config['hide_default_language']) {
            $result['redirect'] = true;
        } else {
            // check if activating language detection is possible
            if(preg_match("#^([a-z]{2}).#i",$referer['host'],$match)) {
                if(mlf_isEnabled($match[1])) {
                    // found language information
                    $referer['host'] = substr($referer['host'], 3);
                }
            }
            if(!$result['internal_referer']) {
                // user coming from external link
                $result['redirect'] = true;
            }
        }
    }
    
    return $result;
}



function mlf_localeForCurrentLanguage($locale){
    global $mlf_config;
    // try to figure out the correct locale
    $locale = array();
    $locale[] = $mlf_config['locale'][$mlf_config['language']].".utf8";
    $locale[] = $mlf_config['locale'][$mlf_config['language']]."@euro";
    $locale[] = $mlf_config['locale'][$mlf_config['language']];
    $locale[] = $mlf_config['windows_locale'][$mlf_config['language']];
    $locale[] = $mlf_config['language'];
    
    // return the correct locale and most importantly set it (wordpress doesn't, which is bad)
    // only set LC_TIME as everyhing else doesn't seem to work with windows
    setlocale(LC_TIME, $locale);
    
    return $mlf_config['locale'][$mlf_config['language']];
}


?>
