<?php
/*
 * File:        scoreboard/save_player.php
 * Version:     1.0
 * Description: Player creation and modification actions for the mobile application
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

if($badge)
{
	$parts = explode("|", $badge);
	
	$barcodeFields = array("uid","ukn1","ukn2","ukn3","first_name","last_name",
		"Job Title","Organization","Major Command","Address1","Address2","Address3",
		"City","State","ZIP","Country","Phone","FAX","Email");

	$neededFields = array("badgeID", "first_name", "last_name");

	$playerInfo = array("playerID" => 0);
	$i = 0;

	foreach($parts as $id => $value)
	{
		if($id == 0 || $id == 4 || $id == 5)
		{  $playerInfo[$neededFields[$i++]] = $value;  }
	}
	$playerInfo["active"] = 1;
}

if(playerExists($playerID, $playerInfo))
{
	$where = "dup";
}
else
{
	$playerID = savePlayer($teamID, $playerInfo);
}

//Where to go and what to do!
switch($where)
{
	case "dup":
		//Duplicate badgeID
		$location .= "player.php?where=dup&teamID=".$teamID;
	break;
	case "add another player":
		//Add New Player
		$location .= "player.php?where=edit&teamID=".$teamID;
	break;
	
	case "save player":
		//Save player and return
		$location = "player.php?where=view&teamID=".$teamID."&playerID=".$playerID;
	break;

	default:
		$location .= "team.php?where=view&teamID=".$teamID;
	break;
}

//Move to next page
header("location:".$location);


function savePlayer($teamID, $playerInfo)
{
	if(is_array($playerInfo))
	{
		//Set player info vars for SP
		foreach($playerInfo as $name => $value)
		{
			$sql .= ":".$name.", ";
			$params[":".$name] = $value;
			if(($name == "playerID") && $value)
			{  $skip = TRUE;  }
		}
		
		//Remove extra characters
		$sql = substr($sql, 0, -2);
		
		//Build and process SP to add/update player data
		$sql = "CALL sp_save_player(".$sql.")";
		$stmt = dbQuery($sql, $params);
		dbExecute($stmt);
		$playerID = dbResult($stmt);
		
		//Clear Vars
		unset($sql);
		unset($params);
		unset($stmt);
		
		if(!$skip)
		{
			//Build and process SP to add player to team
			$sql = "CALL sp_add_team_member(:teamID, :playerID)";
			$params = array(
									":teamID" => $teamID, 
									":playerID" => $playerID
								);
			$stmt = dbQuery($sql, $params);
			dbExecute($stmt);
		}
		return $playerID;
	}
}

function playerExists($playerID, $playerInfo)
{
	global $dbh;
	$exists = false;
	$sql = "SELECT IF((IFNULL(playerID, 0) > 0), TRUE, FALSE) FROM view_player_info WHERE badgeID = :badgeID";
	$param = array(":badgeID" => $playerInfo["badgeID"]);
	$stmt = dbQuery($sql, $param);
	dbExecute($stmt);
	$exists = (bool)dbResult($stmt);
	return $exists;
}

?>