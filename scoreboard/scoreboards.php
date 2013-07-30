<?php
/*
 * File:        scoreboard/scoreboards.php
 * Version:     1.0
 * Description: Scoreboards by catgory viewport for the mobile application
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
$section = "Scoreboard by Category";
$pageCSS = "scoreboard";

require_once('../inc/header.php');

?>
<div class="print_only">
	<small>GENERATED ON: <?=date("d M Y", strtotime("now"));?></small>
  <h2><?=PAGETITLE?></h2>
</div>
<div id="scoreboard_content" class="ui-body ui-body-a">	
<?php
	//$sqlAll = "SELECT team_name, points, attempt_time, scenario_type FROM view_scoreboard ORDER BY points DESC, attempt_time DESC";
	$sqlAll = "SELECT vts.team_name, vts.attempt_time, vts.points, t.scenario_type 
		FROM view_team_score vts 
		INNER JOIN team t ON vts.teamID = t.teamID 
		WHERE vts.points IS NOT NULL ";
	$sql = "SELECT scenarioID FROM view_scenario ORDER BY scenarioID";
	$stmt = dbQuery($sql);
	dbExecute($stmt);
	$IDs = dbAllRows($stmt, "names");
	foreach($IDs as $row => $scenario)
	{
		$where = " AND scenario_type = :scenarioID";
		$order = " ORDER BY vts.points DESC, vts.attempt_time ASC";
		displayScoreboard($sqlAll.$where.$order, $scenario["scenarioID"]);
	}
?>
	</div>
  </div>
</div>
<div class="print_only">
  <small>GENERATED ON: <?=date("d M Y", strtotime("now"));?></small>
</div>
<?php
require_once('../inc/footer.php');

function displayScoreboard($sql, $scenarioID = 0)
{
	global $dbh;
	
	if($scenarioID)
	{  $param[":scenarioID"] = $scenarioID;  }
	$stmt = dbQuery($sql, $param);
	dbExecute($stmt);
	$rows = dbAllRows($stmt, "names");
	if((bool)$scenarioID)
	{
		$title = "Scoreboard for ".getScenarioName($scenarioID);
		$color = "d";
	}

	switch($scenarioID)
	{
		case 1:
			echo "\n".'<div class="ui-grid-a">'."\n".'<div class="ui-block-a">';
		break;

		case 2:
			echo "\n</div>\n".'<div class="ui-block-b">'."\n";
		break;

		case 3:
			echo "\n</div>\n"."\n".'</div><br /><br /><div class="ui-grid-a">'.'<div class="ui-block-a">'."\n";
		break;

		case 4:
			echo "\n  </div>\n".'<div class="ui-block-b">';
		break;

		default:
			$title = "Overall Scoreboard";
			$color = "e";
		break;
	}

	?>
  	<fieldset class="ui-body ui-body-<?php echo $color; ?> ui-corner-all">
      <h3 class="center"><?php echo $title;  ?></h3>
      <hr />
  <?php

	if(count($rows))
	{
		?>
    	<table>
        <thead>
          <tr>
            <th>RANK</th>
            <th>TEAM NAME</th>
            <th>SCORE</th>
            <th>ATTEMPT TIME</th>
            <?php
              if(!$scenarioID)
              {  ?><th>CATEGORY</th><?php  }
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
            foreach($rows as $rank => $row)
            {
              ?>
                <tr>
                  <td align="right"><?php echo $rank + 1; ?></td>
                  <td><?php echo $row["team_name"]; ?></td>
                  <td align="right"><?php echo $row["points"]; ?></td>
                  <td><?php echo sec2hms($row["attempt_time"]); ?></td>
                  <?php
                    if(!$scenarioID)
                    {  ?><td><?php echo getScenarioName($row["scenario_type"]); ?></td><?php  }
                  ?>
                </tr>
              <?php
            }
          ?>
        </tbody>
      </table>
		<?php
	}
	else
	{  ?>  <center>No teams have scored yet.</center>  <?php  }
	?>
  	</fieldset>
  <?php	
}
?>