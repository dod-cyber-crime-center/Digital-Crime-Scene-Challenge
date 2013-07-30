<?php
/*
 * File:        scoreboard/save_score.php
 * Version:     1.0
 * Description: Score saving actions for the mobile application
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

if($where == "disqualify team")
{
	$sql = "CALL sp_disqualify_team(:teamID)";
	$param[":teamID"] = $teamID;
	$stmt = dbQuery($sql, $param);
	dbExecute($stmt);
	header("location:".$basePath."queue/index.php");
}

$sql = "SELECT points FROM view_team_score WHERE teamID = :teamID";
$param[":teamID"] = $teamID;
$stmt = dbQuery($sql, $param);
dbExecute($stmt);
$scored = dbResult($stmt);
if(is_array($found) && !$scored)
{
	$sql = "CALL sp_found_item(:itemID, :teamID, :time_found, :wrong)";
	foreach($found as $itemID => $info)
	{
		if($info["time_found"] > "")
		{
			$params[":itemID"] = $itemID;
			$params[":teamID"] = $teamID;
			$params[":time_found"] = $info["time_found"];
			$params[":wrong"] = ($info["wrong"] != "" ? $info["wrong"] : 0);
			$stmt = dbQuery($sql, $params);
			dbExecute($stmt);
			unset($params);
			unset($stmt);
		}
	}

	$sql = "CALL sp_calc_team_score(:teamID, :start_time, :attempt_time)";
	if($teamInfo["start_time"] != "" && $teamInfo["attempt_time"] != "")
	{
		$params[":teamID"] = $teamID;
		$params[":start_time"] = $teamInfo["start_time"];
		$params[":attempt_time"] = $teamInfo["attempt_time"];
		$stmt = dbQuery($sql, $params);
		dbExecute($stmt);
		unset($params);
		unset($stmt);
	}
}
else
{  $infoMsg = "Teams data cannot be saved twice.";  }
?>