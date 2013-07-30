<?php
/*
 * File:        scoreboard/daily_score.php
 * Version:     1.0
 * Description: Scoreboard viewport for current day for the mobile application
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
$section = "Daily Scoreboard";
$pageCSS = "";

require_once('../inc/header.php');

$sql = "SELECT DISTINCT t.teamID, t.team_name, t.points,  
					DATE_FORMAT((SELECT MAX(time_found) FROM found WHERE t.teamID = teamID), '%Y-%c-%d') as played_on 
					FROM team t 
					INNER JOIN found f ON f.teamID = t.teamID
					WHERE disqualified = FALSE
					GROUP BY t.team_name, played_on
					ORDER BY played_on ASC, t.points DESC";
$stmt = dbQuery($sql);
dbExecute($stmt);
?>

<fieldset class="ui-body ui-body-e ui-corner-all">
	<h3 style="text-align: center;">Daily Scoreboard</h3>
  	<style>
			#winners > *
			{  font-size: 2em;  }
			
			#winners td,
			#winners th
			{  padding: 2px 1em;  }

			#winners td
			{
				padding: 2px 1em;
				border-top: 1px solid #999;
				border-bottom: 1px solid #999;
			}
		</style>
    <table id="winners">
    	<tr>
      	<th>RANK</th>
	<th>Team Name</th>
        <th>Team Score</th>
        <th>Day Played</th>
      </tr>
    	<?php
				$lastDay = "";
				while($row = dbRow($stmt, "names"))
				{
					$playedOn = date("m-d-Y", strtotime($row["played_on"]));
					$timePlayed = date("m-d-Y", strtotime($row["played_on"]));
					if($playedOn != $lastDay)
					{
						$i = 0;
						$lastDay = $playedOn;
						?>
            	<tr>
              	<td colspan="4" style="background-color: #000; color: #FFF;">
                	<center>
										<?php echo date("l - d M", strtotime($row["played_on"])); ?>
                  </center>
                </td>
              </tr>
            <?php
					}
					?>
            <tr>
             	<td><?php echo ++$i; ?> </td>
							<td><a href="<?=BASEPATH?>team/team.php?where=view&teamID=<?php echo $row["teamID"]; ?>"><?php echo $row["team_name"]; ?></a></td>
              <td><?php echo $row["points"]; ?></td>
              <td><?php echo $timePlayed; ?></td>
            </tr>
          <?php
				}
		?>
    </table>
</fieldset>
<?php
require_once('../inc/footer.php');
?>