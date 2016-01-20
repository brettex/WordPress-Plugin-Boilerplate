<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h1>WP Newsletter</h1>
    <h2>Options</h2>
    <p>Configure the options below to control how youd like youre news letter to appear on the front end.  The Placeholder values correspond to the plugin defaults.</p>
<form method="post" action="options.php">
	<?php wp_nonce_field('update-options') ?>

        <p><strong>Company Logo:</strong><br />
        <em>By default, we try look in your theme directory for your logo, but update the path below if its somewhere else!</em>
	<input type="text" name="wp_newsletter_logo" id="logo" size="90" value="<?php echo get_option('wp_newsletter_logo'); ?>" placeholder="img/logo.png"/>
    <?php 
        if(get_option('wp_newsletter_logo') != ''){
            $img = get_option('wp_newsletter_logo');
        } else {
            $img = 'img/logo.png';
        }
    ?>
    <img src="<?php echo get_bloginfo('template_url')."/".$img; ?>" id="logo-preview" width="150px" style="display:block;margin:20px;"/>
    </p>
    	<p><strong>Background Color:</strong><br />
	<input type="text" name="wp_newsletter_bg_color" size="90" value="<?php echo get_option('wp_newsletter_bg_color'); ?>" placeholder="#ffffff"/></p>
    	<p><strong>Body Width:</strong><br />
	<input type="text" name="wp_newsletter_body_width" size="90" value="<?php echo get_option('wp_newsletter_body_width'); ?>" placeholder="620px"/></p>
    <p><strong>Font Family:</strong><br />
	<input type="text" name="wp_newsletter_font_family" size="90" value="<?php echo get_option('wp_newsletter_font_family'); ?>" placeholder="Verdana, Arial, sans-serif"/></p>
	    <p><strong>Link Color:</strong><br />
	<input type="text" name="wp_newsletter_link_color" size="90" value="<?php echo get_option('wp_newsletter_link_color'); ?>" placeholder="#e50354"/></p>
    	<p><strong>Heading Color:</strong><br />
	<input type="text" name="wp_newsletter_heading_color" size="90" value="<?php echo get_option('wp_newsletter_heading_color'); ?>" placeholder="#6353ec"/></p>
    <p><strong>Button Background Color:</strong><br />
	<input type="text" name="wp_newsletter_button_bg_color" size="90" value="<?php echo get_option('wp_newsletter_button_bg_color'); ?>" placeholder="#e50354"/></p>
    <p><strong>Button Text Color:</strong><br />
	<input type="text" name="wp_newsletter_button_text_color" size="90" value="<?php echo get_option('wp_newsletter_button_text_color'); ?>" placeholder="#ffffff"/></p>
    <p><strong>Date Format:</strong><br />
    <em>Use <a href="http://php.net/manual/en/function.date.php">php date()</a> parameters</em><br />
	<input type="text" name="wp_newsletter_button_date_format" size="90" value="<?php echo get_option('wp_newsletter_date_format'); ?>" placeholder="M Y"/></p>
    <p><strong>URL Tracking Paramters:</strong><br />
    <em>Append content links with Google tracking parameters <a href="https://support.google.com/analytics/answer/1033867?hl=en">Help</a></em><br />
	<input type="text" name="wp_newsletter_google_parameters" size="90" value="<?php echo get_option('wp_newsletter_google_parameters'); ?>" placeholder="utm_source=newsletter&utm_medium=email&utm_campaign=month-year"/></p>
    <p><strong>Optimize for Outlook</strong><br />
    <em>Will include Conditional statements in code to optimize formatting for Microsoft Outlook Clients</em><br />
	<input type="checkbox" name="wp_newsletter_outlook" size="90" value="<?php echo get_option('wp_newsletter_outlook'); ?>" /></p>
    

    
	<p><input type="submit" name="Submit" value="Update Options" class="button add-new-h2" /></p>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="wp_newsletter_logo,wp_newsletter_link_color,wp_newsletter_outlook,wp_newsletter_google_parameters,wp_newsletter_date_format,wp_newsletter_button_text_color,wp_newsletter_button_bg_color,wp_newsletter_heading_color,wp_newsletter_link_color,wp_newsletter_font_family,wp_newsletter_body_width,wp_newsletter_bg_color" />

	</form>
    <script>
        jQuery(document).ready( function($){
           jQuery('#logo').on('focus blur keyup', function(){
               var src = "<?php echo get_bloginfo('template_url');?>"+"/"+$(this).val();
               jQuery('#logo-preview').attr('src', src);
           });
        });
    </script>
</div><!-- end wrap -->

