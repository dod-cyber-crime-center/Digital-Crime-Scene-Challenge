<?php
/*
 * File:        inc/dbGet.php
 * Version:     1.0
 * Description: Shared database SELECT functions for Crime Scene Challenge
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
 
//Functions used to get a single column of team data.
function getTeamName($teamID)
{
	$sql = "SELECT team_name FROM view_team_info WHERE teamID = :teamID";
	$param = array(":teamID" => $teamID);
	$stmt = dbQuery($sql, $param);
	dbExecute($stmt);
	$result = dbResult($stmt);
	return $result;
}


//Functions used to get a single column of player data.
function getPlayerName($playerID)
{
	$sql = "SELECT CONCAT(first_name, ' ', last_name) FROM view_player_info WHERE playerID = :playerID";
	$param = array(":playerID" => $playerID);
	$stmt = dbQuery($sql, $param);
	dbExecute($stmt);
	$result = dbResult($stmt);
	return $result;
}
?>