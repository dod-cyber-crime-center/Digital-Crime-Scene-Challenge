<?php
/*
 * File:        scoreboard/score.php
 * Version:     1.0
 * Description: Selection interface to view teams or overall scoreboard 
 *              for the mobile application
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
 
require_once('save_score.php');

/**************************************************
  THESE FIELDS ARE REQUIRED ON ALL DISPLAY PAGES!  
**************************************************/
$section = "Crime Scene Team Score Card";
$pageCSS = "";

require_once('../inc/header.php');

if(is_array($found) && !$infoMsg)
{
	?>
	<div class="boxed ui-body ui-body-a">
		<?php require_once('scorecard.php'); ?>
    <a data-role="button" data-inline="true" data-ajax="false" href="<?php echo $basePath; ?>queue/index.php">In the Queue</a>
 	 	<a data-role="button" data-inline="true" data-ajax="false" href="<?php echo $basePath; ?>scoreboard/scoreboard.php">Overall Scoreboard</a>
	</div>
<?php
}
else
{
	?>
  	<div class="boxed ui-body ui-body-a">
    	<?php
				if(!$infoMsg)
				{  $infoMsg = "No team data present.  Please choose an option below.";  }
				infoMsg($infoMsg);
			?>
      <a data-role="button" data-inline="true" data-ajax="false" href="<?php echo $basePath."scoreboard/team_score.php?teamID=".$teamID; ?>">Team's Scorecard</a>
      <a data-role="button" data-inline="true" data-ajax="false" href="<?php echo $basePath; ?>scoreboard/scoreboard.php">Overall Scoreboard</a>
		</div>
  <?php
}
require_once('../inc/footer.php');
?>