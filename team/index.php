<?php
/*
 * File:        team/index.php
 * Version:     1.0
 * Description: Team and player general viewport for the mobile application
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
$section = "Crime Scene Team Information";
$pageCSS = "";

require_once('../inc/header.php');

$menu = array(
					"Create Team" => "team/team.php",
					"View/Edit Team Information" => "team/team_list.php",
					"Change Team Members Affilation" => "team/member_list.php",
					"Participating Team Queue" => "queue/index.php"
				);
?>
<div data-role="fieldcontain" data-ajax="false" class="ui-body ui-body-a">
  <ul data-role="listview">
    <?php
      foreach($menu as $title => $link)
      {  
        ?>
          <li><a data-ajax="false" href="<?php echo $basePath.$link; ?>"><?php echo $title; ?></a></li>
        <?php
      }
    ?>
  </ul>
</div>
<?php  require_once('../inc/footer.php');  ?>