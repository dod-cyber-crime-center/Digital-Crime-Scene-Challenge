<?php
/*
 * File:        inc/includes.php
 * Version:     1.0
 * Description: Loads CSS and Jquery Libraries for mobile web application
 * Author:      DoD Cyber Crime Center (www.dc3.mil)
 * Released:    09 Jul 2013
 * Language:    PHP5, JQuery 1.6.2, JQuery-UI 1.8.3, JQuery-Mobile 1.0b3
 * License:     GPL v2
 * Project:     DC3 Digital Crime Scene Challenge
 * Contact:     info@dc3.mil
 * 
 * This project constitutes a work of the United States Government and is 
 * not subject to domestic copyright protection under 17 USC § 105.
 *
 * This project is free software, under either the GPL v2 license or a
 * BSD style license, as supplied with this software.
 * 
 * This project is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. See the license files for details.
 * 
 * For additional details and disclaimers, please refer to LEGAL.txt
 */
 
//Are we using a mobile browser?
if(preg_match('/mobile/i', $_SERVER['HTTP_USER_AGENT']))
{  define("MOBILE", TRUE);  }
else
{  define("MOBILE", TRUE);  }

//This array contains the cascading style sheets in order of which they will be attached.
//The value in the array should be the target from the root of the styles folder.
$cssFileName = array(
								"jquery.countdown.css",
								"default.css",
								"default-color.css"
							);
if(MOBILE)
{  $cssFileName[] = "jquery.mobile-1.0b3.css";  }

//This array contains the javascript files in order of which they will be attached.
//The value in the array should be the target from the root of the scripts folder.
$jsFileName = array(
								"jquery.js",
								"jquery-ui-1.8.13.custom.min.js",
								"jquery.countdown.js",
								"date.format.js"
							);
if(MOBILE)
{  $jsFileName[] = "jquery.mobile-1.0b3.js";  }

//Attach css files.
foreach($cssFileName as $fileName)
{  echo "  \n".'<link rel="stylesheet" href="'.$basePath.'styles/'.$fileName.'" />';  }

//Include a special CSS file based on the pageCSS variable.
if($pageCSS)
{  echo "  \n".'<link rel="stylesheet" href="'.$basePath.'styles/'.$pageCSS.'.css" />';  }

//Attach javascript files.
foreach($jsFileName as $fileName)
{  echo "  \n".'<script type="text/javascript" src="'.$basePath.'scripts/'.$fileName.'"></script>';  }

?>
<script>
	<?php
		if(MOBILE)
		{
			?>
				$(document).ready(function()
				{  $.mobile.ajaxLinksEnabled = false;  });
			<?php
		}
	?>
	
	function timedRefresh()
	{  setTimeout("location.reload(true);", 5000);  }
</script>