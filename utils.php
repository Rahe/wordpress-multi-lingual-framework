<?php

function mlf_isEnabled($lang) {
    global $mlf_config;
    return in_array($lang, $mlf_config['enabled_languages']);
}

?>
