<?php
 /* 
	
	@date 2015-1-23
	@author Primitive Spark
	@description Used a HTML Email or
	newsletter while levaraging WordPress 
	backend then importing into a 3rd party CRM like
    iContact or MailChimp

*/
 
 // Uncomment these to Turn on Errors for this page
	//ini_set('display_errors','on');
	//error_reporting(E_ALL);
		
if (have_posts()): while (have_posts()) : the_post();

// If its a bot, let it scrape the page, otherwise, redirect to the plant savvy landing page
// with th e newsletter pre-opened in a lightbox.

// Define the URL
if(isset($_GET['preview'])){$preview = true;} else{ $preview = false;}
if(isset($_GET['load'])){$load = true; $preview = true;} else{ $load = false;}
$url = $_SERVER['SERVER_NAME']; 

if(!preg_match('/FacebookExternalHit|LinkedInBot|googlebot|Facebot|robot|spider|crawler|curl|^$/i', $_SERVER['HTTP_USER_AGENT'])){
	// Dont redirect if its being loaded via lightbox
	if(!$preview || (!$load && !$preview)){
		#header("Location:http://".$url.$ext);
	}
}

/**
    DEFINE THE CONTENT
**/
$page_url = get_permalink();
$title = get_the_title();
$sub_title = get_post_meta($post->ID, '_wp_newsletter_sub_title', true);
$date = get_the_date($dateFormat);
$URL_date = get_the_date('M-Y');
$meta_description = $title;
$twitter_text = $title;


/**
    TEMPLATE GLOBAL DEFAULT SETTINGS
**/
/* Background Color */
$bodyBG = "#fff";
/* Body width */
$bodyWidth = "620px";
/* Link Color */
$linkColor = "#e50354";
/* Heading Color */
$headingColor = "#6353ec";
/* Button BG Color */
$buttonColor = "#e50354";
/* Button Text Color */
$buttonTextColor = "#ffffff";
/* Date format */
$dateFormat = 'M Y';
/* Google URL Parameters */
$google_parameters = "?utm_source=newsletter&utm_medium=email&utm_campaign=".$URL_date;
/* Default Image Directory */
$imgDir = plugins_url( 'img/', __FILE__ );

/* Default Social Images */
$facebook = $imgDir.'fb.jpg';
$email = $imgDir.'email.jpg';
$twitter = $imgDir.'twitter.jpg';
$google = $imgDir.'google.jpg';
$linkedin = $imgDir.'linkedin.jpg';

/* LOGO */
/* Default - looks for logo.png in active theme folder */
$logo =  get_bloginfo('template_url').'/';
$logoImg = 'img/logo.png';


/**
    USER OPTIONS
**/
//User Defined Options cound in wp-newsletter-admin-display.php
/* Heading Color */ 
if("" !== get_option('wp_newsletter_heading_color') && get_option('wp_newsletter_heading_color') != NULL)
$linkColor = get_option('wp_newsletter_heading_color');
/* Body width */ 
if("" !== get_option('wp_newsletter_body_width') && get_option('wp_newsletter_body_width') != NULL)
$linkColor = get_option('wp_newsletter_body_width'); 
/* Background Color */
if("" !== get_option('wp_newsletter_body_bg') && get_option('wp_newsletter_body_bg') != NULL)
$linkColor = get_option('wp_newsletter_body_bg');
/* Link Color */
if("" !== get_option('wp_newsletter_link_color') && get_option('wp_newsletter_link_color') != NULL)
$linkColor = get_option('wp_newsletter_link_color');
/* Link Color */
if("" !== get_option('wp_newsletter_button_bg_color') && get_option('wp_newsletter_button_bg_color') != NULL)
$buttonColor = get_option('wp_newsletter_button_bg_color');
/* Link Color */
if("" !== get_option('wp_newsletter_button_text_color') && get_option('wp_newsletter_button_text_color') != NULL )
$buttonTextColor = get_option('wp_newsletter_button_text_color');
/* Date Format */
if(get_option('wp_newsletter_date_format') !== "" && get_option('wp_newsletter_date_format') != NULL )
$dateFormat = get_option('wp_newsletter_date_format');
/* Date Format */
if(get_option('wp_newsletter_logo') !== "" && get_option('wp_newsletter_logo') != NULL )
$logoImg = get_option('wp_newsletter_logo');

/* Define Button Styles */
$buttonStyles ='font-weight:bold;font-size:14px;color:#fefefe;display:inline-block;line-height:36px;min-width:170px;background:#008ed5;text-decoration:none;text-align:center;-webkit-text-size-adjust:none;mso-hide:all;';


/**
    LAYOUT OPTIONS
**/
//Is Promo area Vertical or Horizontal
$orientation = get('default_orientation'); 


//Custom Meta fields
if(get('meta_information_meta_facebook_description')){
	$meta_description = get('meta_information_meta_facebook_description');
}
if(get('meta_information_twitter_share_cop')){
	$twitter_text = get('meta_information_twitter_share_copy');
}
// Get the Featured Image Attributes
//$imageAttributes = wp_get_attachment( get_post_thumbnail_id($post->ID) );


/** Clean up <p> tags and inline style to <a> tags **/
function addStyle($string){
    global $buttonStyles, $linkColor, $headingColor, $google_parameters;
    //Check to see if theres a button
   /* if(strpos($string, 'class="button"')){ echo "true";
        $string = str_replace('class="button"', 'class="button" style="'.$buttonStyles.'" ', $string); //Add inline style to <a> tags
    } else {
	    $string = str_replace('<a ', '<a style="color:'.$linkColor.';" ', $string); //Add inline style to <a> tags
    } */
    //$string = str_replace('<h', '<h style="'.$headingColor.';" ', $string); //Add inline style to <h> tags
    $string = preg_replace('/<h(.*?)>/', '<h$1 style="color:'.$headingColor.';">', $string);
	$string = str_replace('<p>', '', $string); //Remove <p> tags - Causes problems with Hotmail, etc
	$string = str_replace('</p>', '<br /><br />', $string); // Convert closeing <p> tags to <br>
	
    
    /** 
        ADD INLINE STYLES TO LINKS
    **/
    $links = substr_count($string, '<a'); //Count how many links in a string

	$offset=0;
    $hrefOffset=0;
	for($i=0;$i<$links;$i++){
		$start =  strpos($string, '<a', $offset); // Begin href
		$end = strpos($string, '</a>', $start); // End href
        $link = substr($string, $start, $end-$start);
        if(strpos($link, 'class="button"')){
            $string = substr_replace($string, ' class="button" style="'.$buttonStyles.'" ', $start+2, 0);
        } else {
            $string = substr_replace($string, ' style="color:'.$linkColor.';" ', $start+2, 0);
        }
		$offset = $end+1;
        
        //Append all in-content links with $google_parameters
        $off =  strpos($string, 'href="', $hrefOffset); // Begin href
		$pos = strpos($string, '"', $off+6); // End href
		$string = substr_replace($string, $google_parameters, $pos, 0); //Append
		$hrefOffset = $pos+1; 
	}
	
	return $string;	
}

function get_the_content_with_formatting ($more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<title> <?php echo $title; ?> - <?php echo $date; ?> | <?php bloginfo('name'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="IE=edge, chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="p:domain_verify" content="a179bcc297a73a146418ce2d8b4c83b2"/>
    <meta name="description" content="<?php echo $meta_description; ?>" />
    <meta name="keywords" content=""  />
    <meta name="og:type" content="website" />
    <meta name="og:image" content="<?php echo $imageAttributes['src'];?>"/>
    <meta name="og:title" content="<?php echo $title; ?> | <?php echo $date; ?>" />
    <meta name="og:description" content="<?php echo $meta_description; ?>" />
    <meta name="og:url" content="<?php echo $page_url; ?>" />
    
    <!-- Font Awesome for Iconography -->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" >
    
    <style type="text/css">
      html, body, td{
          color:#424242;
          font-family:Arial, sans-serif;
          font-size:13px;
          line-height:22px;
      }
    a, a:link{
        color:#008ed5;
    }
    a:hover, a:link:hover{text-decoration:underline !important;}
    a.btn:hover, a.btn:link:hover{
        background:#e23e26 !important;
        text-decoration:none !important;
    }
    @media only screen and (min-width: 630px) {
    .left-side{
        width: 53% !important;
        max-width:300px;
    }
    .right-side{
        width:46% !important;
        max-width:240px
    }
    .hero{
        display:block !important;
        height:auto !important;
        width:100% !important; 
    }
    .paddingTop{padding-top:0 !important;}
    .fullWidth{width:30% !important;}
    .hideMobile{width:70% !important;}
    }
    @media only screen and (min-width: 450px) {
        .main-wrapping-table{
            max-width:100%;
            padding:20px;
        }
    }
    @media only screen and (max-width: 600px) {
        
    }
    @media only screen and (max-width: 450px) {
    .hideMobile{
        width:0 !important;
        display:table-cell;
    }
     }
    </style>
    <!--[if mso]>
    <style type="text/css">
    html, body{width:650px !important;}
    .body-text,
    .main-wrapping-table{
        font-family:Arial, sans-serif !important;
        width:650px !important;
    }
	h1{
		font-size:28px !important;
		line-height:32px !important;
		margin-top:10px !important;
	}
	.fullWidth{padding-top:0 !important;}
    </style>
    <![endif]-->
    
    <!--[if gte mso 14]>
    <style type="text/css">
    html, body{width:650px !important;}
    .main-wrapping-table{padding:20px;width:650px !important;}
    .left-side{
        width: 52% !important;
        max-width:300px;
    }
    .right-side{
        width:46% !important;
        max-width:240px
    }
    </style>
<![endif]-->
</head>
<body style="margin:0 auto;-webkit-text-size-adjust:none;">
<!-- For Adding Custom Preview Text -->
<div class="hide-outlook" style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;width:1px;height:1px;opacity:0;overflow:hidden;">
  Preview Text: 2015 Client Leadership Roundtable with Core Logic            
</div>
<!-- end custom preview text -->
    <table class="main-wrapping-table" align="center" style="font-family:Arial;color:#424242;font-size:13px;max-width:650px;margin:0 auto;line-height:19px;background:#f1f0eb;" cellpadding="0" cellspacing="0">
        <tr><td align="center">
        <table style="max-width:650px;" width="100%" cellpadding="0" cellspacing="0">
        <!-- header -->
        <tr><td>
            <table style="background:#fff;" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width:130px;padding:0 10px;" align="left">
                        <a href="<?php bloginfo('url'); ?>" target="_blank">
                            <img src="<?php echo $logo.$logoImg ?>" width="100%" style="max-width:132px;" alt="<?php bloginfo('name');?>" border="0"/>
                        </a>
                    </td>
                    <td align="right" style=""><?php echo $date; ?></td>
                </tr>
            </table>
      </td></tr>
      <!-- end header -->
      <?php if(has_post_thumbnail()): ?>
      <?php $promoImage = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), 'original');?>
      <!-- promo area -->
      <tr>
      	<td>
        	<table width="100%" style="max-width:671px;" cellpadding="0" cellspacing="0">
            	<tr>
                    <td valign="top">
                    	<img src="<?php echo $promoImage[0]; ?>" alt="" border="0" width="100%" />
                    </td>
                </tr>
        	</table>
        <!-- end promo -->
        <?php endif; ?>
       </td>
      </tr>     
      <tr>
         <td>
             <!-- Main Table Item -->
             <table align="left" width="100%" cellpadding="0" cellspacing="0" style="background:#fff;padding:20px;">
             <!-- Title -->
                 <tr>
                    <td style="padding-bottom:20px;">
                    <h1 style="color:<?php echo $headingColor; ?>"><?php echo $title; ?></h1>
                    </td>
                </tr>
              <!-- body -->
                  <tr>
                    <td valign="top">
                    <?php echo addStyle(get_the_content_with_formatting()); ?> 
            <!--[if mso]>
              <v:rect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#" style="height:36px;v-text-anchor:middle;width:170px;" fillcolor="#008ed5" strokecolor="#008ed5">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:Helvetica, Arial,sans-serif;font-size:16px;">REGISTER NOW &#9656;</center>
              </v:rect>
            <![endif]-->
            <!--[if !mso]><!-->
                    </td>
                 </tr>
             </table>             
             
             <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff;">
                 <tr>
                    <td style="padding:20px;">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur pellentesque facilisis diam eu sollicitudin. Etiam elementum augue ac nibh sollicitudin vulputate. <br /><br />Mauris auctor magna sit amet tortor cursus ultricies. Mauris erat risus, feugiat ut est eget, consequat euismod lorem. <br /><br />Aliquam vel eleifend turpis, a scelerisque magna. Vestibulum aliquet a mauris a cursus. Etiam eget ligula consectetur, interdum arcu non, vestibulum lorem. Praesent id imperdiet dolor. Cras enim ex, rhoncus in consequat ut, tempor nec diam.<br /><br /> 
                    
                    Best regards,<br /><br />
                    <strong>John Doe</strong><br />
                    CTO<br />
                    Direct:310-555-1234<br />
                    Mobile:310-555-1235<br />
                    <a href="mailto:salesrep@corelogic.com" style="color:#008ed5;text-decoration:none;">salesrep@corelogic.com</a><br />
                    <a href="#" style="color:#008ed5;text-decoration:none;" title="Linkedin">LinkedIn</a>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:20px;">
                    <!--[if mso]>
                      <v:rect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#" style="height:36px;v-text-anchor:middle;width:170px;" fillcolor="#008ed5" strokecolor="#008ed5">
                        <w:anchorlock/>
                        <center style="color:#ffffff;font-family:Helvetica, Arial,sans-serif;font-size:16px;">REGISTER NOW &#9656;</center>
                      </v:rect>
                    <![endif]-->
                    <!--[if !mso]><!-->
                    <a href="#" title="REGISTER NOW" class="hide-outlook" target="_blank" style="font-weight:bold;font-size:14px;color:#fefefe;display:inline-block;line-height:36px;min-width:170px;background:#008ed5;text-decoration:none;text-align:center;-webkit-text-size-adjust:none;mso-hide:all;">REGISTER NOW &#9656;</a><!--<![endif]-->
                    </td>
                </tr>
             </table>
             <table width="100%" style="max-width:700px;" cellpadding="0" cellspacing="0" align="center">
                <tr>
                	<td class="monty" valign="middle" width="20%" style="font-size:28px;line-height:32px;color:#7a6f66;font-family:'Montserrat', Verdana;text-align:right;">
                        SHARE:
                    </td>
                    <td width="10px">&nbsp;</td>
                    <td width="75%">
                        <a href="mailto:Enter Email(s) Here?subject=Monrovia - Plant Savvy&body=Dear Friend, %0A%0AI think you would enjoy this Monrovia Newsletter below %0A%0A<?php echo $page_url; ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo $email ?>" width="15%" style="max-width:100px" border="0" alt="email"/>
                        </a>
                        <a href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $page_url; ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo $facebook ?>" width="15%" style="max-width:100px" border="0" alt="Facebook" />
                        </a>
                        <a href="https://twitter.com/share?url=<?php echo $page_url; ?>&text=<?php echo $twitter_text; ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo $twitter ?>" width="15%" style="max-width:100px" border="0" alt="Twitter" />
                        </a>
                        <a href="https://plus.google.com/share?url=<?php echo $page_url; ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo $google ?>" width="15%" style="max-width:100px" border="0" alt="Google" />
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $page_url; ?>&title=Plant Savvy <?php echo $date; ?>&summary=<?php echo get('introduction_title'); ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo $linkedin ?>" width="15%" style="max-width:100px" border="0" alt="LinkedIn" />
                        </a>
                    </td>
                </tr>
                <tr><td colspan="5" style="line-height:20px;border-bottom:2px solid #e4e4e4">&nbsp;</td></tr>
                <tr><td colspan="5" style="line-height:30px;">&nbsp;</td></tr>
             </table>
             <table width="100%" cellpadding="0" cellspacing="0">
                 <tr>
                    <td style="font-size:10px;line-height:16px;padding:10px;" align="center">
                        © <?php echo date('Y');?> <?php echo bloginfo('name'); ?>. All rights reserved.<br />
                    </td>
                </tr>
             </table>
        </td>
      </tr>
      </table>
      </td></tr>
    </table>
</body>
</html>
<?php endwhile; endif; ?>