<?php 

function mlf_updateGettextDatabases($force = false, $only_for_language = '') {

    if(!is_dir(WP_LANG_DIR)) {
        if(!@mkdir(WP_LANG_DIR)){                    
            return false;
        }
    }
    $next_update = mlf_get_option('next_update_mo');
    $locale_list = mlf_get_option('locale');
  
    if(time() < $next_update && !$force) 
        return true;
        
    update_option('mlf_next_update_mo', time() + 7*24*60*60);
    
    foreach($locale_list as $lang => $locale) {
        
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
    
    $default_language = mlf_get_option('default_language');
    $url_mode = mlf_get_option('url_mode');
    $hide_default_language  = mlf_get_option('hide_default_language');
    
    $home = mlf_parseURL(get_option('home'));
    $home['path'] = trailingslashit($home['path']);
    $referer = mlf_parseURL($referer);
    
    $result = array();
    $result['language'] = $default_language;
    $result['url'] = $url;
    $result['original_url'] = $url;
    $result['host'] = $host;
    $result['redirect'] = false;
    $result['internal_referer'] = false;
    $result['home'] = $home['path'];
    
    switch($url_mode) {
        case "path":
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
        case "domain":
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
        if(empty($referer['host']) || !$hide_default_language) {
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
    global $admin_language;
 
    $locale_list = mlf_get_option('locale');
    $windows_locale_list = mlf_get_option('windows_locale');
    
    // try to figure out the correct locale
    $locale = array();
    $locale[] = $locale_list[$admin_language].".utf8";
    $locale[] = $locale_list[$admin_language]."@euro";
    $locale[] = $locale_list[$admin_language];
    $locale[] = $windows_locale_list[$admin_language];
    $locale[] = $admin_language;
  
    // return the correct locale and most importantly set it (wordpress doesn't, which is bad)
    // only set LC_TIME as everyhing else doesn't seem to work with windows
    setlocale(LC_TIME, $locale);
    
    return $locale_list[$admin_language];
}


function mlf_convertURL($url='', $lang='', $forceadmin = false) {
	global $mlf_config;
	
	// invalid language
	if($url=='') $url = esc_url($mlf_config['url_info']['url']);
	if($lang=='') $lang = $mlf_config['language'];
	if(defined('WP_ADMIN')&&!$forceadmin) return $url;
	if(!mlf_isEnabled($lang)) return "";
	
	// & workaround
	$url = str_replace('&amp;','&',$url);
	$url = str_replace('&#038;','&',$url);
	
	// check for trailing slash
	$nottrailing = (strpos($url,'?')===false && strpos($url,'#')===false && substr($url,-1,1)!='/');
	
	// check if it's an external link
	$urlinfo = mlf_parseURL($url);
	$home = rtrim(get_option('home'),"/");
	if($urlinfo['host']!='') {
		// check for already existing pre-domain language information
		if($mlf_config['url_mode'] == 'subdomain' && preg_match("#^([a-z]{2}).#i",$urlinfo['host'],$match)) {
			if(mlf_isEnabled($match[1])) {
				// found language information, remove it
				$url = preg_replace("/".$match[1]."\./i","",$url, 1);
				// reparse url
				$urlinfo = mlf_parseURL($url);
			}
		}
		if(substr($url,0,strlen($home))!=$home) {
			return $url;
		}
		// strip home path
		$url = substr($url,strlen($home));
	} else {
		// relative url, strip home path
		$homeinfo = mlf_parseURL($home);
		if($homeinfo['path']==substr($url,0,strlen($homeinfo['path']))) {
			$url = substr($url,strlen($homeinfo['path']));
		}
	}
	
	// check for query language information and remove if found
	if(preg_match("#(&|\?)lang=([^&\#]+)#i",$url,$match) && mlf_isEnabled($match[2])) {
		$url = preg_replace("#(&|\?)lang=".$match[2]."&?#i","$1",$url);
	}
	
	// remove any slashes out front
	$url = ltrim($url,"/");
	
	// remove any useless trailing characters
	$url = rtrim($url,"?&");
	
	// reparse url without home path
	$urlinfo = mlf_parseURL($url);
	
	// check if its a link to an ignored file type
	$ignore_file_types = preg_split('/\s*,\s*/', strtolower($mlf_config['ignore_file_types']));
	$pathinfo = pathinfo($urlinfo['path']);
	if(isset($pathinfo['extension']) && in_array(strtolower($pathinfo['extension']), $ignore_file_types)) {
		return $home."/".$url;
	}
	
	// dirty hack for wp-login.php
	if(strpos($url,"wp-login.php")!==false) {
		return $home."/".$url;
	}
	
	switch($mlf_config['url_mode']) {
		case 'path':	// pre url
			// might already have language information
			if(preg_match("#^([a-z]{2})/#i",$url,$match)) {
				if(mlf_isEnabled($match[1])) {
					// found language information, remove it
					$url = substr($url, 3);
				}
			}
			if(!$mlf_config['hide_default_language']||$lang!=$mlf_config['default_language']) $url = $lang."/".$url;
			break;
		case 'subdomain':	// pre domain 
			if(!$mlf_config['hide_default_language']||$lang!=$mlf_config['default_language']) $home = preg_replace("#//#","//".$lang.".",$home,1);
			break;
		default: // query
			if(!$mlf_config['hide_default_language']||$lang!=$mlf_config['default_language']){
				if(strpos($url,'?')===false) {
					$url .= '?';
				} else {
					$url .= '&';
				}
				$url .= "lang=".$lang;
			}
	}
	
	// see if cookies are activated
	if(!$mlf_config['cookie_enabled'] && !$mlf_config['url_info']['internal_referer'] && $urlinfo['path'] == '' && $lang == $mlf_config['default_language'] && $mlf_config['language'] != $mlf_config['default_language'] && $mlf_config['hide_default_language']) {
		// :( now we have to make unpretty URLs
		$url = preg_replace("#(&|\?)lang=".$match[2]."&?#i","$1",$url);
		if(strpos($url,'?')===false) {
			$url .= '?';
		} else {
			$url .= '&';
		}
		$url .= "lang=".$lang;
	}
	
	// &amp; workaround
	$complete = str_replace('&','&amp;',$home."/".$url);

	// remove trailing slash if there wasn't one to begin with
	if($nottrailing && strpos($complete,'?')===false && strpos($complete,'#')===false && substr($complete,-1,1)=='/')
		$complete = substr($complete,0,-1);
	
	return $complete;
}


add_filter('redirect_canonical',			'mlf_checkCanonical', 10, 2);

function mlf_checkCanonical($redirect_url, $requested_url) {
	// fix canonical conflicts with language urls
    
    //var_dump(mlf_convertURL($redirect_url),mlf_convertURL($requested_url)); die;
	if(mlf_convertURL($redirect_url)==mlf_convertURL($requested_url)) 
		return false;
	return $redirect_url;
}

add_filter('the_content', 'mlf_add_link_to_other_languages');

function mlf_add_link_to_other_languages($content) {

    global $post, $mlf_config;
    
    $other_languages = mlf_get_tranlsations_ids($post->ID, $post->post_type);
    
    // We have to temporarily change the language to retrieve unflitered permalinks
    $currentLanguage = $mlf_config['language'];
    $mlf_config['language'] = $mlf_config['default_language'];
    
    foreach($other_languages as $l) {
    
        if ($l) {
            
            $r .= "<a href='" . get_permalink($l) . "'>" . get_permalink($l) . "</a>";
        
        }
    
    }
    
    //restore language
    $mlf_config['language'] = $currentLanguage;
    
    return $content . $r;

}

?>
