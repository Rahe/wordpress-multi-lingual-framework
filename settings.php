<?php

function mlf_page_admin() { 
    global $plugin_url, $plugin_prefix , $plugin_name;

    $enabled_languages = mlf_get_option('enabled_languages');
    $language_name_list = mlf_get_option('language_name');
    
    foreach ($enabled_languages as $language){
        $language_label[$language] = $language_name_list[$language];
    }    
    
    
    $options = array (
        array( "name" => $plugin_name." Options",
               "type" => "title"),
               
        array( "name" => "General",
               "type" => "section"),
        
        array( "type" => "open"),    
         
        array( "name" => "Default Language",
            "desc" => "Select the site default language",
            "id" => $plugin_prefix."default_language",
            "type" => "select",
            "options" => $enabled_languages,
            "label" =>   $language_label,
            "std" => "blue"),
            
        array( "type" => "close"),
        array( "name" => "Advanced",
            "type" => "section"),
        array( "type" => "open"),
        
        array( "name" => "URL Modification Mode",
            "desc" => "Choose a url mode",
            "id" => $plugin_prefix."url_mode",
            "type" => "radio",
            "options" => array("query" => "Use Query Mode (?lang=en)", "path" => "Use Path Mode (puts /en/ in front of URL)"),
            "std" => "Choose a category"),
            
        array( "type" => "close")
    );        
   
    $i=0;
?>
    <div class="wrap mlf_wrap">
        <h2><?php echo $plugin_name; ?> Settings</h2>
     
        <div class="mlf_opts">
    
            <form method="post" action="options.php">
            
            <?php settings_fields( 'multi-language-settings-group' ); ?>
            
            <?php foreach ($options as $value) {

            switch ( $value['type'] ) {
         
                case "open":
                    break;
            
                case "close":
            ?>             
                </div>
            </div>
            <br />

            <?php 
                break;
             
                case "title":
            ?>            
            <p>To easily use the <?php echo $themename;?> theme, you can use the menu below.</p>
             
            <?php 
                break; 
                
                case "text":
            ?>

            <div class="mlf_input mlf_text">
            
                <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" />
                <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
             
             </div>
            
            <?php 
                break;
             
                case "textarea":
            ?>

            <div class="mlf_input mlf_textarea">
            
                <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                <textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>
                <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>             
             </div>
              
            <?php
                break;
             
                case "select":
            ?>

            <div class="mlf_input mlf_select">
                <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                
                <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                <?php foreach ($value['options'] as $option) { ?>
                        <option <?php if (get_settings( $value['id'] ) == $option) { echo 'selected="selected"'; } ?> value="<?php echo $option; ?>">
                        <?php if (isset($value['label'])) { ?>
                            <?php echo $value['label'][$option]; ?>
                        <? }else{ ?>
                            <?php echo $option; ?>
                        <? } ?>                            
                        </option>
                <?php } ?>
                </select>

                <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
            </div>
            
            <?php
                break;
             
                case "checkbox":
            ?>

            <div class="mlf_input mlf_checkbox">
                <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                
                <?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
                <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />

                <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
             </div>
             
            <?php
                break;
             
                case "radio":
            ?>

            <div class="mlf_input mlf_radio">
                <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

                <ul>
                    <?php foreach ($value['options'] as $option => $label) { ?>                    
                        <li><input type="radio" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="<?php echo $option ?>"  <?php if (get_settings( $value['id'] ) == $option) { echo 'checked="checked"'; } ?>><?php echo $label ?></li>
                    <?php } ?>  
                </ul>
                <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
             </div>
            <?php 
                break; 
                case "section":

                $i++;

            ?>

            <div class="mlf_section">
                <div class="mlf_title"><h3><img src="<?php echo $plugin_url; ?>images/trans.png" class="inactive" alt="""><?php echo $value['name']; ?></h3><span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="Save changes" />
                </span><div class="clearfix"></div></div>
                <div class="mlf_options">
             
            <?php 
                break;
            }   // close switch
        }   // close foreach
        ?>             
        
            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>   
            </form>  
        </div> 

<?php
}
?>
