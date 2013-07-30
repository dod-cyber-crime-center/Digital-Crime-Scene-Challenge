<?php
/*
 * File:        team/player.php
 * Version:     1.0
 * Description: Player management viewport for the mobile application
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
$section = "Add Player";
$pageCSS = "";

require_once('../inc/header.php');

//If there is a playerID, load player data.
if($playerID)
{
	$sql = "SELECT badgeID, first_name, last_name, active FROM view_player_info WHERE playerID = :playerID";
	$param[":playerID"] = $playerID;
	$stmt = dbQuery($sql, $param);
	dbExecute($stmt);
	$playerInfo = dbRow($stmt, "names");
}
else
{  $playerInfo["active"] = TRUE;  }

$bottomView = "";
$topView = "";

//Is the form readonly?
switch($where)
{
	case "view":
		$readonly = 'readonly="readonly"';
		$viewBoth = false;
	break;

	case "dup":
		infoMsg("This player already exists in the system.  To change the players team affiliation, save the team as is, and proceed to the team members menu item.  There select the player from the team member list and change their affiliation.");
	case "add":
	default:
		$readonly = "";
		$viewBoth = true;
	break;

	case "edit":
		$readonly = "";
		$viewBoth = false;
	break;
}

//Display Player data form
?>
<form name="player_checkin" id="player_checkin" action="save_player.php" data-ajax="false" class="ui-body ui-body-a" method="post">
 	<input type="hidden" name="teamID" value="<?php echo $teamID; ?>" />
  <input type="hidden" name="playerInfo[playerID]" value="<?php echo $playerID; ?>" />
  <h3>Add Player to Team: <em><?php echo getTeamName($teamID)." <small>(#".$teamID.")</small>"; ?></em></h3>
  <?php
		/********************************************************************************************************/
		/********************************************************************************************************/
		$viewBoth = FALSE;  // FOR DEFCON ONLY!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		/********************************************************************************************************/
		/********************************************************************************************************/
		if($viewBoth)
		{
			?>
				<div data-role="collapsible-set">
					<div data-role="collapsible" data-collapsed="false" >
						<h3>Badge Entry</h3>
						<script>
							function scan_badge(scanfield)
							{
								var count = scanfield.value.split(/\|/g).length - 1;
								var limit1 = 7;
								if(count == limit1)
								{
									var value = $("#scan1").val();
									$("#scan").val(value);
								}
								var limit2 = 19;
								if(count == limit2)
								{  document.player_checkin.submit();  alert("Processing...");  }
							}
						</script>
						<label for="badge1">Scan Badge: </label>
						<input id="scan1" name="badge1" type="text" onKeyUp="scan_badge(this);" />
						<input id="scan" type="hidden" name="badge" />
						<br />
					</div>
				<div data-role="collapsible" data-collapsed="true" >
				<?php
			}
		?>
    <h3>Manual Entry</h3>
    <?php
			if($playerInfo["badgeID"])
			{  $badgeID = $playerInfo["badgeID"];  }
			else
			{
				$sql = "SELECT (IFNULL(COUNT(playerID), 0) + 1) FROM player";
				$stmt = dbQuery($sql);
				dbExecute($stmt);
				$badgeID = "Manual BadgeID ".(int)dbResult($stmt);
			}
		?>
		<input id="badgeID" name="playerInfo[badgeID]" type="hidden" max="250" value="<?php echo $badgeID ?>" readonly />
		<label for="first_name">First Name: </label>
		<input id="first_name" name="playerInfo[first_name]" type="text" max="40" value="<?php echo $playerInfo["first_name"]; ?>" <?php echo $readonly; ?> />
		<br /><br />
		<label for="last_name">Last Name: </label>
		<input id="last_name" name="playerInfo[last_name]" type="text" max="40" value="<?php echo $playerInfo["last_name"]; ?>" <?php echo $readonly; ?> />
		<br /><br />
		<?php
			if($readonly)
			{
				?>
					<label for="active">Active: </label>
					<input id="active" name="playerInfo[active]" type="text" readonly value="<?php echo readonlyValue($playerInfo["active"]); ?>" />
				<?php
			}
			else
			{  dropdowns($id = "active", $name = "playerInfo[active]", $label = "Active", $type = "slider", $playerInfo["active"]);  }
		if($viewBoth)
		{  echo "</div></div>";  }
	?>
  <br />
  <div data-role="fieldcontain">
		<?php
      if($readonly)
      {
        $link = $basePath."team/player.php?where=edit&teamID=".$teamID."&playerID=".$playerID;
        ?>
          <a data-ajax="false" href="<?php echo $link; ?>" data-role="button" data-theme="b" name="where" value="Edit" data-inline="true" data-icon="gear" >Edit</a>
        <?php
      }
      else
      {
        ?>
          <input type="submit" name="where" value="Add another Player" data-inline="true" data-icon="arrow-r" />
          <input type="submit" name="where" value="Save Player" data-inline="true" data-icon="gear" />
         <?php
      }
      $link = $basePath."team/team.php?teamID=".$teamID;
    ?>
    <a data-ajax="false" href="<?php echo $link; ?>" data-role="button" name="where" data-inline="true" data-icon="back" />Back to Team</a>
  </div>
</form>
<script>
  $(document).ready( function() {$("#scan1").focus();} );
</script>
<?php
require_once('../inc/footer.php');  ?>