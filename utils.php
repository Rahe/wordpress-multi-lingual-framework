<?php

function mlf_isEnabled($lang) {
    
    $enabled_languages = mlf_get_option('enabled_languages');
    
    return in_array($lang, $enabled_languages);
}

function mlf_parseURL($url) {
    $r  = '!(?:(\w+)://)?(?:(\w+)\:(\w+)@)?([^/:]+)?';
    $r .= '(?:\:(\d*))?([^#?]+)?(?:\?([^#]+))?(?:#(.+$))?!i';

    preg_match ( $r, $url, $out );
    $result = @array(
        "scheme" => $out[1],
        "host" => $out[4].(($out[5]=='')?'':':'.$out[5]),
        "user" => $out[2],
        "pass" => $out[3],
        "path" => $out[6],
        "query" => $out[7],
        "fragment" => $out[8]
        );
    return $result;
}

function mlf_startsWith($s, $n) {
    if(strlen($n)>strlen($s)) return false;
    if($n == substr($s,0,strlen($n))) return true;
    return false;
}


?>
