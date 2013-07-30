<?php
/*
 * File:        team/team.php
 * Version:     1.0
 * Description: Team management viewport for the mobile application
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
$section = "Add/Edit Team Information";
$pageCSS = "";

require_once('../inc/header.php');

//Find existing team members
if($teamID)
{
	$sql = "SELECT vti.team_name, vti.scenario_type, vti.score, 
						vti.attempt_time, vti.disqualified, IFNULL((SELECT count(playerID) FROM team_members WHERE teamID = vti.teamID), 0) as player_count 
						FROM view_team_info vti 
						WHERE vti.teamID = :teamID";
	$param[":teamID"] = $teamID;
	$stmt =	dbQuery($sql, $param);
	dbExecute($stmt);
	$info = dbRow($stmt, "names");
	$teamInfo["team_name"] = $info["team_name"];
	$teamInfo["scenario_type"] = $info["scenario_type"];
	$teamInfo["score"] = (is_null($info["score"]) ? "N/A" : $info["score"]);
	$teamInfo["time"] = (is_null($info["attempt_time"]) ? "N/A" : $info["attempt_time"]);
	$teamInfo["disqualified"] = ($info["disqualified"] == 1 ? "YES" : "NO");
	$playerCount = $info["player_count"];
}

if($where == "view")
{  $readonly = 'readonly="readonly"';  }
else
{  $readonly = "";  }

?>
<form action="save_team.php" data-ajax="false" class="ui-body ui-body-a" method="post">
  <?php
		if($teamID)
		{  ?><input type="hidden" name="teamID" value="<?php echo $teamID; ?>" /><?php  }
	?>
  <div data-role="fieldcontain">
  	<label for="team_name">Team Name: </label>
  	<input id="team_name" name="teamInfo[team_name]" type="text" max="75" value="<?php echo $teamInfo["team_name"]; ?>" <?php echo $readonly; ?>/>
	</div>
  <?php
		if($teamID)
		{
			if($readonly)
			{
				?>
          <div data-role="fieldcontain">
            <label for="scenario">Scenario: </label>
            <input id="scenario" name="teamInfo[scenario_type]" type="text" max="75" value="<?php echo getScenarioName($teamInfo["scenario_type"]); ?>" <?php echo $readonly; ?>/>
          </div>
          <div data-role="fieldcontain">
            <label for="disqualified">Disqualified: </label>
            <input id="disqualified" name="teamInfo[disqualified]" type="text" max="75" value="<?php echo $teamInfo["disqualified"]; ?>" <?php echo $readonly; ?>/>
          </div>
        <?php
			}
			else
			{
				$sql = "SELECT scenarioID, scenario_name FROM view_scenario";
				dropdowns($id = "scenario", $name = "teamInfo[scenario_type]", $label = "Scenario", $type = "radio-horizontal", $teamInfo["scenario_type"], $sql);
				dropdowns($id = "disqualified", $name = "teamInfo[disqualified]", $label = "Disqualified", $type = "slider", $teamInfo["disqualified"]);
			}
			?>
      	<div data-role="fieldcontain">
          <label for="score">Score: </label>
          <input id="score" name="teamInfo[score]" type="text" max="25" value="<?php echo $teamInfo["score"]; ?>" readonly />
        </div>
        <div data-role="fieldcontain">
          <label for="time">Time: </label>
          <input id="time" name="teamInfo[time]" type="text" max="25" value="<?php echo $teamInfo["time"]; ?>" readonly />
        </div>
      <?php
		}
		else
		{
			$sql = "SELECT scenarioID, scenario_name FROM view_scenario";
			dropdowns($id = "scenario", $name = "teamInfo[scenario_type]", $label = "Scenario", $type = "radio-horizontal", $teamInfo["scenario_type"], $sql);
		}		

		//List the team members.
		if($playerCount)
		{
			$sql = "SELECT playerID, player_name FROM view_team_members WHERE teamID = :teamID";
			$param[":teamID"] = $teamID;
			$stmt = dbQuery($sql, $param);
			dbExecute($stmt);
			mobileDataTable($stmt, $teamID);
		}
	?>
	<div data-role="fieldcontain">
  	<input type="submit" name="where" value="Add a Player" data-inline="true" data-icon="arrow-r" data-iconpos="right" />
    <?php
		if($readonly)
		{
			?>
      	<input type="submit" name="where" value="Edit" data-inline="true" data-icon="gear" data-iconpos="right" data-theme="e" />
      <?php
		}
		else
		{
			if($teamID)
			{
			  ?>
      	  <input type="submit" name="where" value="Save Team" data-inline="true" data-icon="gear" data-iconpos="right" data-theme="e" />
        <?php
			}
		}
		if($teamID && ($teamInfo["score"] && $teamInfo["attempt_time"]))
		{
			?>
      	<input type="submit" name="where" value="Recalculate Score" data-inline="true" data-icon="gear" data-iconpos="right" data-theme="b" />
      <?php
		}
		?>
    <a data-role="button" data-inline="true" data-icon="back" data-ajax="false" href="<?php echo $basePath; ?>team/team_list.php">Back to Team List</a>
  </div>
  <br />
</form>
<?php  require_once('../inc/footer.php');  ?>

<?php

function mobileDataTable($stmt, $teamID)
{
	?>
		<div data-role="content">
			<div class="content-primary">	
				<ul data-role="listview" data-filter="true" data-inset="true" data-filter-placeholder="Search players..." data-filter-theme="b">
        <?php
					while($show = dbRow($stmt, "names"))
					{
						echo '<li><a data-ajax="false" href="'.BASEPATH.'team/player.php?where=view&teamID='.$teamID.'&playerID='.$show["playerID"].'">'.$show["player_name"].'</a></li>';
					}
				?>
        </ul>
      </div>
    </div>
  <?php
}

?>