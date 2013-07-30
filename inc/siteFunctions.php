<?php
/*
 * File:        inc/siteFunctions.php
 * Version:     1.0
 * Description: Custom PHP functions for the mobile web application
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
 
//Creates a mobile drop down slider or range based on type
function dropdowns($id, $name, $label, $type, $passedValue = 1, $sql = NULL, $params = NULL)
{
	?>
   	<div data-role="fieldcontain">
  <?php

	if($sql)
	{
		$stmt = dbQuery($sql, $params);
		dbExecute($stmt);
		$optionValues = dbAllRows($stmt, "num");
	}
	else
	{
		$optionValues = array(
								"Yes" => 1, 
								"No" => 0
							);
	}

	$startGroup = "";
	$endGroup = "";
	
	switch($type)
	{
		case "select":
			?>
      	<label for="<?php echo $id; ?>"><?php echo $label; ?>: </label>
        <select data-native-menu="false" id="<?php echo $id; ?>" name="<?php echo $name; ?>">
        	<?php
						foreach($optionValues as $row => $info)
						{
							$optionValue = $info[0];
							$optionName = $info[1];
							if($optionValue == $passedValue)
							{  $selected = 'selected="selected"';  }
							else
							{  $selected = "";  }
							?>
              	<option <?php echo $selected; ?> value="<?php echo $optionValue; ?>"><?php echo $optionName; ?></option>
              <?php
						}
					?>
        </select>
      <?php
		break;

		case "radio-horizontal":
			$hor = 'data-type="horizontal"';
		case "radio":
			?>
      	<fieldset data-role="controlgroup" <?php echo $hor; ?>>
        	<legend>Choose a <?php echo $label; ?></legend>
          <?php
						foreach($optionValues as $row => $option)
						{
							$optionName = $option[1];
							$optionValue = $option[0];
              if($passedValue == $optionValue)
              {  $checked = 'checked="checked"';  }
              else
              {  $checked = "";  }
              echo "\n".'<input type="radio" id="'.$name.'-'.$optionValue.'" name="'.$name.'" value="'.$optionValue.'" '.$checked.' />';
							echo "\n".'<label for="'.$name.'-'.$optionValue.'">'.$optionName.'</label>';
            }
					?>
        </fieldset>
      <?php
		break;
		
		case "slider":
			?>
      	<label for="<?php echo $id; ?>"><?php echo $label; ?>: </label>
        <select id="<?php echo $id; ?>" name="<?php echo $name; ?>" data-role="<?php echo $type; ?>" data-inline="true">
          <?php
            foreach($optionValues as $optionName => $optionValue)
            {
              if($passedValue == $optionValue)
              {  $selected = 'selected="selected"';  }
              else
              {  $selected = "";  }
              echo "\n".'<option '.$selected.' value="'.$optionValue.'">'.$optionName.'</option>';
            }
          ?>
        </select>
      <?php
		break;

		default:
			## Readonly TEXT
			if(!$optionValues[0][1])
			{  $optionValues[0][1] = " -- NO ".strtoupper($label)." -- ";  }
			$optionName = $optionValues[0][1];
			$optionValue = $optionValues[0][0];
			?>
      	<label for="<?php echo $id; ?>"><?php echo $label; ?>: </label>
      	<input type="text" id="<?php echo $id; ?>" name="<?php echo $optionValue; ?>" value="<?php echo $optionName; ?>" readonly />
      <?php
		break;
	}
	?>
  	</div>
  <?php
}

//Returns a readonly approved value
function readonlyValue($value)
{
	switch(strtolower($value))
	{
		case 0:
			$newValue = "No";
		break;

		case 1:
			$newValue = "Yes";
		break;

		default:
			$newValue = ucwords($value);
		break;
	}
	return $newValue;
}

//Returns time it took to complete investigation
function getTimeTaken($teamID)
{
	$sql = "SELECT attempt_time FROM view_team_score WHERE teamID = :teamID";
	$stmt = dbQuery($sql, array(":teamID" => $teamID));
	dbExecute($stmt);
	$attemptTime = dbResult($stmt);
	return sec2hms($attemptTime);
}

//Returns the name of the scenario the team belongs to
function getScenarioType($teamID)
{
	$sql = "SELECT vs.scenario_name 
					FROM view_team_info vti
					LEFT JOIN view_scenario vs ON vs.scenarioID = vti.scenario_type 
					WHERE vti.teamID = :teamID";
	$stmt = dbQuery($sql, array(":teamID" => $teamID));
	dbExecute($stmt);
	return dbResult($stmt);
}

//Returns the name of the scenario
function getScenarioName($scenarioID)
{
	$sql = "SELECT scenario_name FROM view_scenario WHERE scenarioID = :scenarioID";
	$stmt = dbQuery($sql, array(":scenarioID" => $scenarioID));
	dbExecute($stmt);
	return dbResult($stmt);
}

//Returns the time in hours, minutes, seconds formatted to a minimal time format
function sec2hms($time)
{
	$hms = "";
	$end = "";
	//Find Hours
	$hrs = intval(intval($time) / 3600);
	//Find Minutes
	$mins = intval(($time / 60) % 60);
	//Find Seconds
	$secs = intval($time % 60);
	
	if($hrs)
	{  $hms .= str_pad($hrs, 2, "0", STR_PAD_LEFT).":";  }
	if($mins)
	{  $hms .= str_pad($mins, 2, "0", STR_PAD_LEFT).":";  }
	$hms .= str_pad($secs, 2, "0", STR_PAD_LEFT);
		
	if($secs)
	{  $end = " seconds";  }
	
	if($mins)
	{  $end = " minutes";  }
	
	if($hrs)
	{  $end = " hours";  }

	return $hms.$end;
}

function getItemTypeName($itemTypeID)
{
	$sql = "SELECT item_type_name FROM view_item_type WHERE item_typeID = :item_typeID";
	$stmt = dbQuery($sql, array(":item_typeID" => $itemTypeID));
	dbExecute($stmt);
	return dbResult($stmt);
}
?>