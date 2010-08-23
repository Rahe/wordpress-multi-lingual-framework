<?php

function mlf_page_admin()
{
     ?>
     <div class="wrap">
           <h2>Multi Language Framework Configuration</h2>
           <p>On this page, you will configure all the aspects of this plugins.</p>
           <form action="" method="post" id="multi-language-framework-form">
                <h3><label for="copyright_text">Default language:</label></h3>
                <p><input type="text" name="default_lang" id="default_lang" value="<?php echo esc_attr( get_option('mlf_default_lang') )?> " /></p>
                <p class="submit"><input type="submit" name="submit"   value="Update options &raquo;" /></p>
                <?php wp_nonce_field('multi_language_admin_options-update'); ?>
           </form>
     </div>
     <?php
}

?>
