<?php
/*
 * File:        team/member_list.php
 * Version:     1.0
 * Description: Team list for management viewport for the mobile application
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
$section = "List of Teams";
$pageCSS = "";

require_once('../inc/header.php');

?>
<div class="ui-body ui-body-a">
	<div data-role="fieldcontain">
    <h3>
    	Create a new team 
      <a data-ajax="false" data-inline="true" data-role="button" data-icon="arrow-r" data-iconpos="right" 
      	href="<?php echo $basePath; ?>team/team.php">Create Team</a>
      <br />
      Select the team to view full information
    </h3>
    <ul data-role="listview" data-filter="true" data-inset="true" data-filter-placeholder="Search team..." data-filter-theme="b">
		<?php
			$sql = "SELECT teamID, team_name FROM team";
			$stmt = dbQuery($sql);
			dbExecute($stmt);
			$teamInfo = array();
			if(dbRowCount($stmt))
			{
				while($show = dbRow($stmt, "names"))
				{
					$teamID = $show["teamID"];
					$teamName = $show["team_name"];
					?>
						<li><a data-ajax="false" href="<?php echo $basePath.'team/team.php?where=view&teamID='.$teamID; ?>"><?php echo $teamName; ?></a></li>
					<?php
				}				
			}
			else
			{  echo "There are no teams created.";  }
    ?>
    </ul>
  </div>
</div>
<?php  require_once('../inc/footer.php');  ?>