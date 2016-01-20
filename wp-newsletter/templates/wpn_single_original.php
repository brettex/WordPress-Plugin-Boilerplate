<?php
 /* 
	
	@date 2015-1-23
	@author Primitive Spark
	@description Used to create Plant Savvy
	newsletters while levaraging WordPress 
	backend then importing into iContact

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
$dateHash = strtolower(get_the_date('M-Y'));
$dateOverride = get_field('newsletter_date');
if(!empty($dateOverride)){
	$dateOverride = get_field('newsletter_date');
	$dateHash = strtolower(date('M-Y', strtotime($dateOverride[1])));
}
$ext = '/plant-savvy-newsletter/#'.$dateHash;
if(!preg_match('/FacebookExternalHit|LinkedInBot|googlebot|Facebot|robot|spider|crawler|curl|^$/i', $_SERVER['HTTP_USER_AGENT'])){
	// Dont redirect if its being loaded via lightbox
	if(!$preview || (!$load && !$preview)){
		header("Location:http://".$url.$ext);
	}
}


//Global defaults
$meta_description = get('introduction_title');
$twitter_text = get('introduction_title');
$page_url = get_permalink();
$date = get_the_date('F Y');
$URL_date = get_the_date('M-Y');
//Date Overrides
$dateOverride = get_field('newsletter_date');
if(!empty($dateOverride)){
	$dateOverride = get_field('newsletter_date');
	//print_r($dateOverride);
	$date = date('F Y', strtotime($dateOverride[1]));
	$URL_date = date('M-Y',strtotime($dateOverride[1]));
}
//Is Promo area Vertical or Horizontal
$orientation = get('default_orientation'); 
$google_parameters = "?utm_source=plantsavvy&utm_medium=email&utm_campaign=".$URL_date."-plant-savvy";

//Custom Meta fields
if(get('meta_information_meta_facebook_description')){
	$meta_description = get('meta_information_meta_facebook_description');
}
if(get('meta_information_twitter_share_cop')){
	$twitter_text = get('meta_information_twitter_share_copy');
}
// Get the Featured Image Attributes
$imageAttributes = wp_get_attachment( get_post_thumbnail_id($post->ID) );

//Get the Main and Sidebar groups
$sidebars = get_group('sidebar_content');
$mains = get_group('main_col_article');
//Loop through main articles for creating an table of contents first
foreach($mains as $main){
	$titles[] = $main['main_col_article_title'][1];
}

/** Clean up <p> tags and inline style to <a> tags **/

function addStyle($string){
	$string = str_replace('<a ', '<a style="color:rgb(129, 157, 15);" data-url="true" ', $string); //Add inline style to <a> tags
	$string = str_replace('<p>', '', $string); //Remove <p> tags - Causes problems with Hotmail, etc
	$string = str_replace('</p>', '<br /><br />', $string); // Convert closeing <p> tags to <br>
	
	//Append all in-content links with $google_parameters
	$instances = substr_count($string, 'href='); //Count how many links in a string
	
	$offset=0;
	for($i=0;$i<$instances;$i++){
		$off =  strpos($string, 'href="', $offset); // Begin href
		$pos = strpos($string, '"', $off+6); // End href
		$string = substr_replace($string, $google_parameters, $pos, 0); //Append
		$offset = $pos+1; 
	}
	
	return $string;	
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<title>Plant Savvy | <?php echo $date; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="IE=edge, chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="p:domain_verify" content="a179bcc297a73a146418ce2d8b4c83b2"/>
    <meta name="description" content="<?php echo $meta_description; ?>" />
    <meta name="keywords" content=""  />
    <meta name="og:type" content="website" />
    <meta name="og:image" content="<?php echo $imageAttributes['src'];?>"/>
    <meta name="og:title" content="Plant Savvy | <?php echo $date; ?>" />
    <meta name="og:description" content="<?php echo $meta_description; ?>" />
    <meta name="og:url" content="<?php echo $page_url; ?>" />
    
	<style type="text/css">
    @import url(http://fonts.googleapis.com/css?family=Montserrat:400,700);
	  html, body, td{
		  color:#514942;
		  font-family:Verdana, Arial, sans-serif;
		  font-size:16px;
		  line-height:22px
	  }
    a, a:link{
        color:#829c23;
    }
    @media only screen and (min-width: 700px) {
	.main-wrapping-table{
		max-width:100%;
	}
	.left-side{
		width: 70%;
		min-width:400px;
		max-width:700px;
	}
	.right-side{
		width:30%;
		min-width:200px
	}
	.side-table table{
		width:100%;
	}
	.mobileSpacer{display:none;}

	.hideMobile{
		display:table-cell !important;
		width:10%;
	}
    
    }
    @media only screen and (min-width: 450px) {
    
     }
    </style>
    <!--[if mso]>
    <style type="text/css">
    html, body{max-width:700px;}
    .body-text,
    .main-wrapping-table,
    .monty {
    	font-family: Verdana, Arial, sans-serif !important;
    }
    #more{
    	width:100%;
        max-width:600px;
        background:#97b529;
        text-align:center;
        height:50px;
        vertical-align:center;
     } 
    </style>
    <![endif]-->
</head>
<body>
    <table class="main-wrapping-table" align="center" style="font-family:'Verdana',Arial;color:#514942;font-size:16px;width:100%;line-height:22px;max-width:100%;" cellpadding="0" cellspacing="0">
    	<tr><td align="center">
    	<table style="max-width:700px;" width="100%" cellpadding="0" cellspacing="0">
        <!-- header -->
    	<tr><td>
        	<table style="font-family:'Montserrat', Verdana;max-width:700px" width="100%" cellpadding="0" cellspacing="0">
            	<tr>
                	<td style="width:50%;" align="left">
                    	<a href="http://www.monrovia.com/<?php echo $google_parameters;?>" target="_blank">
                    		<img src="http://www.monrovia.com/wp-content/uploads/logo.png" width="100%" style="max-width:384px;" alt="Monrovia"/>
                        </a>
                    </td>
                    <td style="width:50%;font-size:24px;color:#7a6f66;letter-spacing:3px;" align="right">
                    	<span style="color:#89a134;">PLANT</span> SAVVY<br /><span style="font-size:14px;letter-spacing:1px;"><?php echo $date; ?></span>
                    </td>
                </tr>
                <tr><td style="line-height:18px;">&nbsp;</td></tr>
            </table>
      </td></tr>
      <!-- end header -->
      <!-- promo area -->
      <tr>
      	<td>
        	<table width="100%" style="max-width:700px;" cellpadding="0" cellspacing="0">
            <?php if($orientation == 'Vertical'){ ?>
            	<tr>
                	<td>
                    <table width="50%" align="left" style="min-width:150px;">
                    	<tr>
                        <td>
                            <a href="<?php echo get('featured_image_link').$google_parameters;?>" target="_blank">
                            <img border="0" src="<?php echo $imageAttributes['src'];?>" alt="<?php echo $imageAttributes['title'];?>" width="100%" style="max-width:340px;"/>
                            </a>
                        </td>
                        <td width="10px">&nbsp;</td>
                        </tr>
                    </table>
                    <table width="50%" align="left" style="min-width:175px;">
                    	<tr>
                				<td style="width:100%">
                                <span class="monty" style="color:#ef8433;font-size:28px;text-transform:uppercase;line-height:32px;font-family:'Montserrat', Verdana;">
									<?php echo get('introduction_title'); ?>
                                </span>
                                </td>
                		</tr>
                		<tr><td style="line-height:8px;">&nbsp;</td></tr>
                		<tr>
                            <td style="width:100%">
                            	<?php echo addStyle(get_the_content_with_formatting()); ?> 
                            </td>
                		</tr>
                    </table>
                    </td>
                </tr>
            <?php } else { ?>
            	<tr>
                	<td align="center" style="width:100%">
                    <a href="<?php echo get('featured_image_link').$google_parameters;?>" target="_blank">
                        <img border="0" src="<?php echo $imageAttributes['src'];?>" alt="<?php echo $imageAttributes['title'];?>" width="100%" style="max-width:700px;" />
                    </a>
                    </td>
                </tr>
                <tr><td style="line-height:8px;">&nbsp;</td></tr>
                <tr>
                	<td style="width:100%">
                    	<span class="monty" style="color:#ef8433;font-size:28px;text-transform:uppercase;line-height:32px;font-family:'Montserrat', Verdana;">
                        <?php echo get('introduction_title'); ?>
                        </span>
                    </td>
                </tr>
                <tr><td style="line-height:8px;">&nbsp;</td></tr>
                <tr>
                    <td style="width:100%"><?php echo addStyle(get_the_content_with_formatting()); ?></td>
                </tr>
                <?php } ?>
                <tr><td style="line-height:15px;">&nbsp;</td></tr>
                <tr>
                	<td align="center" id="more">
                    <a href="<?php echo get('featured_image_link').$google_parameters;?>" style="text-decoration:none;color:#ffffff;background:#97b529;width:90%;max-width:600px;display:block;text-align:center;line-height:50px;font-size:22px;">READ MORE »</a>
                    </td>
                </tr>
                <tr><td style="line-height:20px;border-bottom:2px solid #e4e4e4">&nbsp;</td></tr>
                <tr><td style="line-height:15px;">&nbsp;</td></tr>
        	</table>
        <!-- end promo -->
       </td>
      </tr>     
      <tr>
         <td>
         	<!-- Main Table Item -->
             <table align="left" width="100%" class="left-side" cellpadding="0" cellspacing="0">
              <?php foreach($mains as $main){ ?>
             <!-- Title -->
             	<tr>
                	<td class="monty" style="font-size:24px;color:#ef8433;text-transform:uppercase;font-family:'Montserrat', Verdana;line-height:28px;" align="left" colspan="3">
                    	<?php echo $main['main_col_article_title'][1]; ?>
                    </td>
                </tr>
                <tr><td style="line-height:10px;">&nbsp;</td></tr>
              <!-- body -->
              	<tr>
                	<td width="30%" align="left" valign="top" style="min-width:150px;max-width:200px;">
                    	<?php 
							// Set the $a closing tag to nothing
							$a = '';
							if(isset($main['main_col_article_image'])){ 
								if($main['main_col_article_image_link'][1]){
									$a = "</a>";
									echo '<a href="'.$main['main_col_article_image_link'][1].$google_parameters.'" target="_blank">';
								} ?> 
							<img src="<?php echo $main['main_col_article_image'][1]['original'];?>" width="95%" style="max-width:200px;" alt="Monrovia"/>
						<?php
							echo $a;
							} // End image check 
						?>
                    </td>
                    <td width="20px">&nbsp;</td>
                    <td width="70%" align="left" valign="top" style="min-width:200px;">
                   	<?php echo addStyle($main['main_col_article_content'][1]); ?>
                    </td>
                </tr>
                <tr><td style="line-height:15px;">&nbsp;</td></tr>
                
                <?php } //End main for loop ?>
             </table>
             <!-- sidebar table -->
             <table align="left" width="100%" class="right-side" cellpadding="0" cellspacing="0">
             	<tr>
                	<td width="0px" style="max-width:30px;display:none;" class="hideMobile">&nbsp;</td>
                    <td width="100%" class="mobile">
                    	<table cellpadding="0" cellspacing="0">
                            <!-- sidebar title -->
                            <tr><td class=="monty" style="width:100%;font-size:20px;color:#89a134;text-transform:uppercase;font-family:'Montserrat', Verdana;">Dig In More</td></tr>
                            <tr><td style="line-height:15px;">&nbsp;</td></tr>
                            <tr><td>
                            <?php foreach($sidebars as $sidebar){ ?>
                                <table width="100%" class="side-table" cellpadding="0" cellspacing="0">
                                    <tr><td>
                                        <table width="30%" align="left" cellpadding="0" cellspacing="0" style="max-width:160px;">
                                            <tr>
                                                <td>
                                            <?php 
                                                // Set the $a closing tag to nothing
                                                $b = '';
                                                if(isset($sidebar['sidebar_content_image'])){ 
                                                    if($sidebar['sidebar_content_image_link'][1]){
                                                        $b = "</a>";
                                                        echo '<a href="'.$sidebar['sidebar_content_image_link'][1].$google_parameters.'" target="_blank">';
                                                    } ?> 
                                                    <img src="<?php echo $sidebar['sidebar_content_image'][1]['original'];?>" width="100%" style="max-width:150px;" alt=""/>
                                            <?php
                                                    echo $b;
                                                } else {
                                                    echo "&nbsp;"; //Empty space
                                                }// End image check 
                                            ?>
                                                </td>
                                                <td width="10px">&nbsp;</td>
                                            </tr>
                                            <tr><td style="line-height:5px;">&nbsp;</td></tr>
                                        </table>
                                        <table width="70%" style="min-width:100px" cellpadding="0" cellspacing="0">
                                            <tr><td style="font-size:14px;line-height:19px;" valign="top">
                                                <span style="color:#f18424;text-transform:uppercase;font-weight:bold;min-width:100px;">
                                                <?php echo $sidebar['sidebar_content_title'][1]; ?>
                                                </span><br />
                                                <?php echo addStyle($sidebar['sidebar_content_content'][1]); ?>
                                            </td></tr>
                                        </table>
                                    </td></tr>
                                    <tr><td style="line-height:22px;">&nbsp;</td></tr>
                                </table>   
                            <?php } //End for loop ?>
                    	</table>
                        <!-- end side bar -->
                    </td></tr>
                </table> 
             </table>
             <table width="100%" style="max-width:700px;" cellpadding="0" cellspacing="0" align="center">
             	<tr><td colspan="5" style="line-height:30px;border-bottom:2px solid #e4e4e4">&nbsp;</td></tr>
                <tr><td colspan="5" style="line-height:15px;">&nbsp;</td></tr>
                <tr>
                	<td class="monty" valign="middle" width="20%" style="font-size:28px;line-height:32px;color:#7a6f66;font-family:'Montserrat', Verdana;text-align:right;">
                        SHARE:
                    </td>
                    <td width="10px">&nbsp;</td>
                    <td width="75%">
                        <a href="mailto:Enter Email(s) Here?subject=Monrovia - Plant Savvy&body=Dear Friend, %0A%0AI think you would enjoy this Monrovia Newsletter below %0A%0A<?php echo $page_url; ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/email/email.jpg" width="15%" style="max-width:100px" border="0" alt="email"/>
                        </a>
                        <a href="http://www.facebook.com/sharer/sharer.php?u=<?php echo $page_url; ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/email/fb.jpg" width="15%" style="max-width:100px" border="0" alt="Facebook" />
                        </a>
                        <a href="https://twitter.com/share?url=<?php echo $page_url; ?>&text=<?php echo $twitter_text; ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/email/twitter.jpg" width="15%" style="max-width:100px" border="0" alt="Twitter" />
                        </a>
                        <a href="https://plus.google.com/share?url=<?php echo $page_url; ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/email/google.jpg" width="15%" style="max-width:100px" border="0" alt="Google" />
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $page_url; ?>&title=Plant Savvy <?php echo $date; ?>&summary=<?php echo get('introduction_title'); ?>" target="_blank" style="text-decoration:none;">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/email/linkedin.jpg" width="15%" style="max-width:100px" border="0" alt="LinkedIn" />
                        </a>
                    </td>
                </tr>
                <tr><td colspan="5" style="line-height:20px;border-bottom:2px solid #e4e4e4">&nbsp;</td></tr>
                <tr><td colspan="5" style="line-height:30px;">&nbsp;</td></tr>
             </table>
             <table width="100%" cellpadding="0" cellspacing="0">
             	<tr>
                	<td align="center">Change your subscription settings <a href="[manage_your_subscription_url]" style="color:#829c23;">here</a>.</td>
                </tr>
                <tr><td style="line-height:10px;">&nbsp;</td></tr>
                <tr>
                	<td align="center">Having trouble viewing this email? <a href="<?php echo $page_url.$google_parameters; ?>" style="color:#829c23;">View it in your browser.</a></td>
                </tr>
                <tr><td style="line-height:50px;">&nbsp;</td></tr>
             </table>
        </td>
      </tr>
      </table>
      </td></tr>
    </table>
</body>
</html>
<?php endwhile; endif; ?>