<?php
/*
 * File:        scoreboard/scoreboard.php
 * Version:     1.0
 * Description: Scoreboard viewport for the mobile application
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
$section = "Scoreboard";
$pageCSS = "";

require_once('../inc/header.php');

$sql = "SELECT COUNT(scenarioID) FROM view_scenario";
$stmt = dbQuery($sql);
dbExecute($stmt);
$scenarioCount = dbResult($stmt);
$stmt = '';

//$sql = "SELECT team_name, points, attempt_time, scenario_type FROM view_scoreboard";
$sql = "SELECT vts.team_name, vts.attempt_time, vts.points, t.scenario_type 
	FROM view_team_score vts 
	INNER JOIN team t ON vts.teamID = t.teamID 
	WHERE vts.points IS NOT NULL 
	ORDER BY vts.points DESC, vts.attempt_time ASC";
$stmt = dbQuery($sql);
dbExecute($stmt);
$rows = dbAllRows($stmt, "names");
?>
<style>
	table,
	td, th
	{
		padding: 10px;
		margin: 5px;
	}

	th
	{  border-bottom: 1px solid #FFF;  }

	.ui-block-a, 
	.ui-block-b
	{
		padding: 10px;
		margin: 0 auto;
	}

	.ui-grid-b center 
	{  min-height: 110px;  }
</style>
<fieldset class="ui-body ui-body-e ui-corner-all">
	<h3 style="text-align: center;">Overall Scoreboard</h3>
	<table>
  	<thead>
    	<tr>
      	<th>RANK</th>
       	<th>TEAM NAME</th>
        <th>SCORE</th>
        <th>ATTEMPT TIME</th>
        <?php
        	if($scenarioCount > 1)
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
            	if($scenarioCount > 1)
							{  ?><td><?php echo getScenarioName($row["scenario_type"]); ?></td><?php  }
						?>
					</tr>
				<?php
			}
		?>
		</tbody>
	</table>
</fieldset>
<?php
require_once('../inc/footer.php');
?>