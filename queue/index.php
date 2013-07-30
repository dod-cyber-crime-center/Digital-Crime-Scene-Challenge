<?php
/*
 * File:        queue/index.php
 * Version:     1.0
 * Description: Team selection for grading viewport for the mobile application
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
$section = "Crime Scene Team Queue";
$pageCSS = "";

require_once('../inc/header.php');

?>
<div class="ui-body ui-body-a">
<?php
$sql = "SELECT teamID, team_name, player_name FROM view_queue";
$stmt = dbQuery($sql);
dbExecute($stmt);
$teamInfo = array();
if(dbRowCount($stmt))
{
	while($show = dbRow($stmt, "names"))
	{
		$teamID = $show["teamID"];
		$teamName = $show["team_name"];
		$playerName = $show["player_name"];
		
		if($teamID == $lastTeamID || is_null($lastTeamID))
		{
			$teamInfo[$teamID]["team_name"] = $teamName;
			$teamInfo[$teamID][$i++] = $playerName;
		}
		else
		{
			$teamInfo[$teamID]["team_name"] = $teamName;
			$teamInfo[$teamID][$i++] = $playerName;
			$i = 0;
		}
		$lastTeamID = $teamID;
	}

	?>
  	<a data-ajax="false" href="<?php echo $basePath; ?>queue/index.php" data-role="button" data-inline="true" data-theme="b" data-icon="refresh">Refresh Page</a>
		<div data-role="fieldcontain">
			<h3>Select the next playing team</h3>
			<ul data-role="listview" data-filter="true" data-inset="true" data-filter-placeholder="Search team..." data-filter-theme="b">
			<?php
				foreach($teamInfo as $teamID => $info)
				{
					$teamName = array_shift($info);
					$text = "";
					$players = array();
					$players = $info;
					foreach($players as $num => $playerName)
					{
						$text .= $playerName." | ";
					}
					$fulltext = $teamName."<br />".substr($text, 0, -3);
					?>
						<li><a data-ajax="false" href="<?php echo $basePath.'queue/item_list.php?teamID='.$teamID; ?>"><?php echo $fulltext; ?></a></li>
					<?php
				}
			?>
			</ul>
		</div>
	<?php
}
else
{
	?>
  	<h3>Nothing in queue.</h3>
    <p>Page will auto refresh every 5 seconds until a team in placed in the queue.  To manually refresh the page click the button below.</p>
    <a data-ajax="false" href="<?php echo $basePath; ?>queue/index.php" data-role="button" data-inline="true" data-theme="b" data-icon="refresh">Refresh Page</a>
    <script>
			$(document).ready(function(){timedRefresh();});
		</script>
  <?php
}
?>
</div>
<?php  require_once('../inc/footer.php');   ?>