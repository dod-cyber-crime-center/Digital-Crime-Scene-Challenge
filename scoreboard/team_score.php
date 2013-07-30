<?php
/*
 * File:        scoreboard/team_score.php
 * Version:     1.0
 * Description: Team rank viewport for the mobile application
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
 
/**************************************************
  THESE FIELDS ARE REQUIRED ON ALL DISPLAY PAGES!  
**************************************************/
$section = "Team Scorecard";
$pageCSS = "";

require_once('../inc/header.php');

?>
<link href="<?=$basePath?>styles/scorecard.css" rel="stylesheet" />
<div data-ajax="false" class="ui-body ui-body-a">
<?php
$admin = TRUE;

if($teamID)
{
	?>
  	<div>
    	<?php require_once('scorecard.php'); ?>
    </div>
  <?php
}
else
{
	$sql = "SELECT teamID, team_name FROM view_team_info ORDER BY score DESC, attempt_time DESC";
	$stmt = dbQuery($sql);
	dbExecute($stmt);
	$rows = dbAllRows($stmt, "names");
	
	if(count($rows))
	{
		?>
    	<div data-role="fieldcontain">
      	<h3>Select Team to view Score Card</h3>
				<ul data-role="listview" data-filter="true" data-inset="true" data-filter-placeholder="Search for team..." data-filter-theme="b">
					<?php
            foreach($rows as $rank => $row)
            {
              $path = $basePath."scoreboard/team_score.php?teamID=".$row["teamID"];
              ?>
                <li><a href="<?php echo $path; ?>"><?php echo $rank + 1; ?> - <?php echo $row["team_name"]; ?></a></li>
              <?php
            }
          ?>
        </ul>
      </div>
		<?php
	}
	else
	{	 ?><h3>No teams have scored yet.</h3><?php  }
}
?>
</div>
<?php  require_once('../inc/footer.php');  ?>