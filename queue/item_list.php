<?php
/*
 * File:        queue/item_list.php
 * Version:     1.0
 * Description: Team grading viewport for the mobile application
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
$section = "Crime Scene Evidence List";
$pageCSS = "";

require_once('../inc/header.php');

$startTimeUTC = strtotime("now");
$startTime = date("m-d-Y H:i:s", strtotime("now"));

//General var
$genSQL = "SELECT itemID, item_name, item_description, point_value, item_typeID  
										FROM view_item_list 
										WHERE ";
global $tried;
$tried = "tried-";
global $gJavaDateFormat;
$gJavaDateFormat = "yyyy-mm-dd H:MM:ss";
global $neg;
$neg = -5;

$sql = "SELECT teamID FROM view_team_info WHERE teamID = :teamID";
$param[":teamID"] = $teamID;
$stmt = dbQuery($sql, $param);
dbExecute($stmt);
$teamID = dbResult($stmt);
if($teamID)
{
?>

<style>
	.ui-grid-b .ui-block-a,
  .ui-grid-b .ui-block-b
  {
    width: 40%;
    margin: 0 4%;
  }
</style>
<form id="myForm" action="<?php echo $basePath."scoreboard/score.php?teamID=".$teamID; ?>" method="post" data-ajax="false" class="ui-body ui-body-a ui-corner-all">
	<fieldset class="ui-body-b ui-corner-bottom ui-corner-right padded">
   	<legend class="ui-body-b ui-corner-top" style="margin: 20px 0 0 0; padding: 10px 10px 5px 10px;">
			<?php echo getTeamName($teamID); ?> (<em><small><?php echo getScenarioType($teamID); ?></small></em>)
    </legend>
    <input type="hidden" name="teamID" value="<?php echo $teamID; ?>" />
    <input type="hidden" id="startTime" name="teamInfo[start_time]" value="<?php echo $startTime; ?>" />
    <input type="hidden" id="attemptTime" name="teamInfo[attempt_time]" value="0" />
    <div id="startButton">
      <button data-theme="g" data-inline="true" type="button" id="start">Start Game</button>
    </div>
    <hr />
    <?php
      $sql = $genSQL."item_typeID IN (2, 3)";
      $stmt = dbQuery($sql);
      dbExecute($stmt);
    ?>
    <div class="ui-grid-b">
      <div class="ui-block-a padded spaced">
        <div data-role="fieldcontain">
          <fieldset data-role="controlgroup">
            <h3>Digital Devices Found</h3>
            <?php
              while($show = dbRow($stmt, "names"))
              {
                $id1 = $show["itemID"];
                $name1 = 'found['.$show["itemID"].'][point_value]';
                $value = $show["point_value"];
                $label = $show["item_name"];
								$description = $show["item_description"];
                $onChange = 'onChange="activateThis(this.id, this.checked); timeStampItem(this.id, this.checked); currentPoints(this.value, this.checked);"';
								if($show["item_typeID"] == 3)
								{
									$onChange = substr($onChange, 0, -1).' keyItems(this.checked); "';
									$keyItems[] = $id1;
								}
								$id2 = "time-".$id1;
                $name2 = 'found['.$show["itemID"].'][time_found]';
                //Call function to create list element...
                listItem($id1, $name1, $value, $label, $description, $onChange, $id2, $name2, 'g');
								$unlock[] = $id1;
              }
            ?>
          </fieldset>
        </div>
      </div>
      <div class="ui-block-b padded spaced">
        <div data-role="fieldcontain">
          <fieldset data-role="controlgroup">
            <h3>Incorrectly Tried Devices</h3>
            <?php
              $stmt = dbQuery($sql);
              dbExecute($stmt);
              while($show = dbRow($stmt, "names"))
              {
                $id1 = "tried-".$show["itemID"];
                $name1 = 'found['.$show["itemID"].'][wrong]';
                $value = 1;
                $label = $show["item_name"];
								$description = $show["item_description"];
                $onChange = 'onChange="deactivateThat(this.id, this.checked); timeStampItem(this.id, this.checked); currentPoints(this.value, this.checked, true);"';
                $id2 = "time-".$id1;
                $name2 = 'found['.$show["itemID"].'][time_tried]';
                //Call function to create list element...
								if($show["item_typeID"] == 2)
								{  listItem($id1, $name1, $value, $label, $description, $onChange, $id2, $name2, 'r');  }
              }
            ?>
          </fieldset>
        </div>
      </div>
    </div>
    <div class="ui-grid-b">
      <div class="ui-block-b padded spaced">
        <div data-role="fieldcontain">
          <fieldset data-role="controlgroup">
            <h3>Evidence Found</h3>
            <?php
              $sql = $genSQL."item_typeID IN (4,5)";
              $stmt = dbQuery($sql);
              dbExecute($stmt);
              while($show = dbRow($stmt, "names"))
              {
                $id1 = $show["itemID"];
                $name1 = 'found['.$show["itemID"].'][point_value]';
                $value = $show["point_value"];
                $label = $show["item_name"];
								$description = $show["item_description"];
                $onChange = 'onChange="timeStampItem(this.id, this.checked); currentPoints(this.value, this.checked);"';
                $id2 = "time-".$id1;
                $name2 = 'found['.$show["itemID"].'][time_found]';
                //Call function to create list element...
                listItem($id1, $name1, $value, $label, $description, $onChange, $id2, $name2);
								if($show["item_typeID"] == 4)
								{  $firstTry = $id1;  }
								elseif($show["item_typeID"] == 5)
								{  $keyItem = $id1;  }
              }
            ?>
          </fieldset>
        </div>
      </div>
      <div class="ui-block-b padded spaced">
        <div data-role="fieldcontain">
          <fieldset data-role="controlgroup">
            <h3>Improper Behavior</h3>
            <?php
              $sql = $genSQL."item_typeID = 1";
              $stmt = dbQuery($sql);
              dbExecute($stmt);
              while($show = dbRow($stmt, "names"))
              {
								$checkbox = true;
                $id1 = $show["itemID"];
                $name1 = 'found['.$show["itemID"].'][point_value]';
                $value = $show["point_value"];
                $label = $show["item_name"];
								$description = $show["item_description"];
								$onChange = 'onChange="timeStampItem(this.id, this.checked); currentPoints(this.value, this.checked);"';
								$id2 = "time-".$id1;
                $name2 = 'found['.$show["itemID"].'][time_found]';
                //Call function to create list element...
                listItem($id1, $name1, $value, $label, $description, $onChange, $id2, $name2, 'e', $checkbox);
								$unlock[] = $id1;
              }
            ?>
          </fieldset>
        </div>
      </div>
    </div>
    <br /><hr />
    <div>
    	<input class="fleft" data-inline="true" type="submit" id="submit" name="where" value="Submit Score" />
    	<input class="fright" data-inline="true" type="submit" id="disqualify" data-theme="e" name="where" value="Disqualify Team" />
    </div>
    <br class="cleared" />
  </fieldset>
</form>
</div>
<style>
	#cpoints
	{
		border: none;
		background: none;
		font-size: 1.5em;
		height: 1.5em;
		line-height: 1.5em;
		color: #FFF;
		width: 50px;
	}

	#cpointslabel
	{
		height: 24px;
		line-height: 24px;
		margin-right: 5px;
		font-size: 1.5em;
	}
</style>
<div id="footer" data-role="footer" data-position="fixed">
	<div data-role="navbar">
		<ul>
			<li>
      	<span id="cpointslabel">Current Points: </span>
        <input id="cpoints" max="3" value="0" readonly />
      </li>
			<li><span id="timer"></span></li>
			<li>
      	<button data-inline="true" type="button" id="pause">Pause/Resume Timer</button>
      </li>
		</ul>
  </div>
</div>
<script>
	var shortly = new Date();
	var eventTime = 900;
	var trys = 0;
	var keyItemFound = 0;
	
	$('#timer').countdown({until: shortly, onExpiry: closeTimer, compact: true, format: 'MS'});

	$('#start').click(function()
	{
		var shortly = new Date();
		$('#startTime').val(dateFormat(shortly, '<?php echo $gJavaDateFormat; ?>'));
		$('#timer').countdown('change', {until: +eventTime});
		$('#startButton').hide();
		<?php
			foreach($unlock as $num => $id)
			{
				?>
					$('#<?php echo $id; ?>').checkboxradio('enable');
				<?php
			}
		?>
		$('#<?php echo $firstTry; ?>').checkboxradio('enable');
		$('#<?php echo $id1; ?>').checkboxradio('enable');
	});
	
	$('#pause').toggle(
		function()
		{
			$('#timer').countdown('pause');
			var remainingTime = $.countdown.periodsToSeconds($('#timer').countdown('getTimes'));
			$('#attemptTime').val(remainingTime);
		},
		function()
		{
			var remainingTime = $('#attemptTime').val();
			$('#timer').countdown('change', {until: +remainingTime});
			$('#timer').countdown('resume');
			$('#attemptTime').val("");
		}
	);

	$('#submit').click(function()
	{
		if(confirmIt() == true)
		{
			closeTimer();
			$('#myForm').submit(true);
		}
		else
		{  $('#myForm').submit(false);  }
  });
	
	$('#disqualify').click(function()
	{
		if(confirmIt() == true)
		{
			closeTimer();
			$('#myForm').submit(true);
		}
		else
		{  $('#myForm').submit(false);  }
  });
	
	function closeTimer()
	{
		$('#timer').countdown('pause');
		$('#timerButtons').hide();
		var stopped = $('#attemptTime').val();
		if(stopped <= 0)
		{
			var remainingTime = $.countdown.periodsToSeconds($('#timer').countdown('getTimes'));
			var attemptTime = (eventTime - remainingTime);
		}
		else
		{  var attemptTime = (eventTime - stopped);  }
		$('#attemptTime').val(attemptTime);
		if(attemptTime == eventTime)
		{
			$('input[type="checkbox"]').checkboxradio('disable');
			alert("Times UP!");
		}
	}
	
	//Disables all the "wrong" item list objects so we can't goof
	$(document).ready(function()
	{
		$('input[type="checkbox"]').checkboxradio('disable');
	});
	
	//Active the "wrong" item list objects
	function activateThis(id, checked)
	{
		if(checked)
		{  $('#<?php echo $tried; ?>' + id).checkboxradio('enable');  }
		else
		{
			$('#<?php echo $tried; ?>' + id).attr('checked', false).checkboxradio('refresh');
			$('#<?php echo $tried; ?>' + id).checkboxradio('disable');
		}
	}
	
	function deactivateThat(id, checked)
	{
		var temp = id;
		id2 = temp.replace("<?php echo $tried; ?>", "");
		if(checked)
		{
			trys += 1;
			$('#' + id2).checkboxradio('disable');
		}
		else
		{
			trys -= 1;
			$('#' + id2).checkboxradio('enable');
		}
		
		if(trys == 0)
		{  $('#<?php echo $firstTry; ?>').checkboxradio('enable');  }
		else
		{  $('#<?php echo $firstTry; ?>').checkboxradio('disable');  }
	}
	
	//Timestamp the Item list
	function timeStampItem(id, checked)
	{
		if(checked)
		{
			var timestamp = new Date();
			$('#time-' + id).val(dateFormat(timestamp, '<?php echo $gJavaDateFormat; ?>'));
		}
		else
		{  $('#time-' + id).val("");  }
	}
	
	function currentPoints(valuePassed, checked, minus)
	{
		var sum = $('#cpoints').val();
		var points = parseInt(sum);
		if(checked == true)
		{
			if(minus == true)
			{  points -= parseInt(valuePassed);  }
			else
			{  points += parseInt(valuePassed);  }
		}
		else
		{
			if(minus == true)
			{  points += parseInt(valuePassed);  }
			else
			{  points -= parseInt(valuePassed);  }
		}
		$('#cpoints').val(points);
	}
	
	function keyItems(checked)
	{
		if(checked == true)
		{  keyItemFound += 1;  }
		else
		{  keyItemFound -= 1;  }

		if(keyItemFound == <?php echo count($keyItems); ?>)
		{  $('#<?php echo $keyItem; ?>').checkboxradio('enable');  }
		else
		{
			var value = $('#<?php echo $keyItem; ?>').val();
			var cvalue = $('#<?php echo $keyItem; ?>:checked').length;
			if(cvalue)
			{  currentPoints(value, cvalue, true);  }
			$('#<?php echo $keyItem; ?>').attr('checked', false).checkboxradio('refresh');
			$('#<?php echo $keyItem; ?>').checkboxradio('disable');
		}
	}

	function confirmIt()
	{
		var answer = confirm("Are you sure you want to do this?");
		return answer;			
	}
</script>
<?php
}
else
{
	infoMsg("Please select a team");
	?>
  	<a data-role="button" data-inline="true" data-ajax="false" data-theme="b" href="<?php echo $basePath; ?>queue/index.php">Team Queue</a>
  <?php
	require_once($basePath."inc/footer.php");
}

//Creates a list of the items.
function listItem($id1, $name1, $value, $label, $description = NULL, $onChange = NULL, $id2 = NULL, $name2 = NULL, $theme = "b")
{
	$theme = ' data-theme="'.$theme.'" ';
	echo '<input type="checkbox" id="'.$id1.'" name="'.$name1.'" value="'.$value.'" class="custom" '.$theme.' '.$onChange.' />'.
			"\n          ".
			'<label for="'.$id1.'">'.$label;
	if($description)
	{  echo "\n          <br /><small>$description</small>";  }
	echo '</label>';
	if($id2 && $name2)
	{  echo "\n          ".'<input type="hidden" id="'.$id2.'" name="'.$name2.'" value="" />';  }
}
?>