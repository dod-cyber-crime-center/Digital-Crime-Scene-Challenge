<?php
/*
 * File:        team/save_team.php
 * Version:     1.0
 * Description: Team creation and modification actions for the mobile application
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
 
require_once('../inc/global.php');

//set location of next screen
$location = $basePath."team/";
switch($where)
{
	case "edit":
		$location .= "team.php?teamID=".$teamID;
	break;

	case "recalculate score":
		$location .= "team.php?where=view&teamID=".$teamID;
		//Call the score function.
	break;

	case "add a player":
		//Add New Player
		$teamID = saveTeamInfo($teamInfo, (int)$teamID);
		$location .= "player.php?view=add&teamID=".$teamID;
	break;

	case "save team":
		//Save team data
		$teamID = saveTeamInfo($teamInfo, (int)$teamID);
		$location .= "team.php?where=view&teamID=".$teamID;
	break;

	default:
		$teamID = saveTeamInfo($teamInfo, (int)$teamID);
		$location .= "team.php?where=view&teamID=".$teamID;
	break;
}

header("location:".$location);

function saveTeamInfo($teamInfo, $teamID = 0)
{
	global $dbh;
//echo "TEAMID: ".$teamID;
	if(is_array($teamInfo))
	{
		//Set SQL and teamID parameter
		if($teamID > 0)
		{  $sql = "CALL sp_save_team_info(:teamID, :team_name, :disqualified, :scenario_type)";  }
		else
		{  $sql = "CALL sp_create_team(:teamID, :team_name, :disqualified, :scenario_type)";  }
		
		$params[":teamID"] = (int)$teamID;
		
		if((string)$teamInfo["team_name"] != "")
		{  $teamName = $teamInfo["team_name"];  }
		else
		{
			$sql = "SELECT MAX(teamID) FROM team";
			$stmt = dbQuery($sql);
			$result = dbResult($stmt) + 1;
			$teamName = "Default Team Name - ".$result;
		}
		$params[":team_name"] = $teamName;
		
		if((int)$teamInfo["disqualified"])
		{  $disqualified = $teamInfo["disqualified"];  }
		else
		{  $disqualified = 0;  }
		$params[":disqualified"] = (int)$disqualified;
		
		if((int)$teamInfo["scenario_type"] > 0)
		{  $scenario_type = $teamInfo["scenario_type"];  }
		else
		{  $scenario_type = 1;  }
		$params[":scenario_type"] = (int)$scenario_type;
//echo "SQL: ".$sql;
//echo "<pre>"; print_r($params); echo "</pre>";		
//Execute query
		$stmt = dbQuery($sql, $params);
		dbExecute($stmt);
		$result = dbResult($stmt);
		return $result;
	}
}
?>