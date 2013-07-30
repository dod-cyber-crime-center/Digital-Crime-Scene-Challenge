<?php
/*
 * File:        team/member_list.php
 * Version:     1.0
 * Description: Team management of its members viewport for the mobile application
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
$section = "List of Members";
$pageCSS = "";

require_once('../inc/header.php');

$url = $basePath."team/member_list.php";
?>
	<div class="ui-body ui-body-a">
		<div data-role="fieldcontain">
      <?php
				switch($where)
				{
					case "remove":
						$teamID = -1;
					case "save":
						//Call Save SP
						$oldTeamID = $teamID;
						$sql = "CALL sp_change_teams(:playerID, :teamID)";
						$params = array(":playerID" => $playerID, ":teamID" => $playerInfo["teamID"]);
						$stmt = dbQuery($sql, $params);
						dbExecute($stmt);
						$teamID = dbResult($stmt);
						unset($stmt);
						$readonly = true;
						$buttonTxt = "Edit";
					case "edit":
					  if(!$readonly)
						{  $readonly = false;  }
						if(!$buttonTxt)
						{  $buttonTxt = "Save";  }
					case "view":
						if($readonly || !isset($readonly))
						{  $readonly = " readonly ";  }
						else
						{  $readonly = "";  }
						
						if(!isset($buttonTxt))
						{  $buttonTxt = "Edit";  }
						
						$sql = "SELECT teamID, team_name FROM team ";
						?>
          		<h3><?php echo getPlayerName($playerID); ?></h3>
              <form action="<?php echo $url; ?>" method="post">
            		<input type="hidden" name="playerID" value="<?php echo $playerID; ?>" />
              	<input type="hidden" name="teamID" value="<?php echo $teamID; ?>" />
                <?php
									if($readonly)
									{
										$sql .= " WHERE teamID = :teamID";
										$params = array(":teamID" => $teamID);
										dropdowns("team_name", "playerInfo[teamID]", "Team Association", "readonly", $teamID, $sql, $params);
									}
									else
									{  dropdowns("team_name", "playerInfo[teamID]", "Team Association", "select", $teamID, $sql);  }
								?>
                <input data-inline="true" type="submit" name="where" value="<?php echo $buttonTxt; ?>" data-icon="gear" />
                <?php
									if($teamID)
									{
										?>
                    	<input data-inline="true" type="submit" name="where" value="Remove " data-icon="delete" />
                    <?php
									}
								?>
                <input data-inline="true" type="submit" name="where" value="Back to Team List" data-icon="back" />
              </form>
            <?php
					break;

					default:
						?>
              <h3>
                Select the member of team to change team affiliation
              </h3>
              <ul data-role="listview" data-filter="true" data-inset="true" data-filter-placeholder="Search team..." data-filter-theme="b">
              <?php
                $sql = "SELECT p.playerID, IFNULL(t.teamID, 0) as teamID, 
                          IFNULL(t.team_name, 'No Team') as team_name, 
                          CONCAT(p.first_name, ' ', p.last_name) as player_name 
                        FROM player p 
                        LEFT OUTER JOIN team_members tm ON tm.playerID = p.playerID 
                        LEFT OUTER JOIN team t ON t.teamID = tm.teamID";
                $stmt = dbQuery($sql);
                dbExecute($stmt);
                if(dbRowCount($stmt))
                {
                  while($row = dbRow($stmt, "names"))
                  {
                    ?>
                      <li>
                        <a href="<?php echo $url."?where=view&playerID=".$row["playerID"]."&teamID=".$row["teamID"]; ?>">
                          <?php echo $row["player_name"]; ?> (<em><?php echo $row["team_name"]; ?></em>)
                        </a>
                      </li>
                    <?php
                  }
                }
                else
                {  echo "There are no players in the system.";  }
              ?>
              </ul>
            <?php
					break;
				}
			?>
    </div>
  </div>
<?php  require_once('../inc/footer.php');  ?>
<?php


				if($playerID && ($teamID == 0 || $teamID))
				{
					?>
            <form>
            	<input type="hidden" name="playerID" value="<?php echo $playerID; ?>" />
              <input type="hidden" name="teamID" value="<?php echo $teamID; ?>" />
            	<?php
								if($where == "view")
								{
									?>
                  	<label for="team">Player Team</label>
                    <input type="text" id="team" value="<?php echo getTeamName($teamID); ?>" readonly />
                    <input type="submit" name="where" value="Edit" />
                  <?php
								}
								else
								{
									dropdowns($id = "team_name", $name = "playerInfo[teamID]", $label = "Player Team", $type = "select", $teamID, $sql);
									?>
                  	<input type="submit" name="where" value="Save" />
                  <?php
								}
							?>
            </form>
          <?php
				}
				else
				{
					
				}
			?>
		</div>
	</div>
<?php  require_once('inc/footer.php');  ?>