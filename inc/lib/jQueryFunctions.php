<?php
/*
 * File:        inc/lib/jQueryFunctions.php
 * Version:     1.5
 * Description: Common output functions using JQuery UI, JQuery Mobile, 
 *              and DataTables for prototype web application development
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
#Outputs text as error notification message.
function errorMsg($body = "Please fill out the entire form before submitting.")
{
	?>
	<div class="ui-state-error ui-corner-all" style="margin: 10px; padding: 0 .7em;">
		<p>
			<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
			<strong>Alert: </strong>
      <?php echo $body; ?>
		</p>
	</div>
	<?php
}

#Outputs text as information notification message.
function infoMsg($body = "An update has been made.")
{
	?>
  <div class="ui-state-highlight ui-corner-all" style="margin: 10px; padding: 0 .7em; border: 1px solid #000;"> 
    <p>
      <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
      <strong>Important: </strong>
      <?php echo $body; ?>
    </p>
  </div>
  <?php
}

#Inserts the jQuery Code to activate sliders on the given name.
function jSliders($name = "slider", $min = 0, $max = 100, $placement = "")
{
	?>
  	<script language="javascript" type="text/javascript">
	  	// Slider
			$(function()
			{
				$("#<?php echo $name; ?>").slider({
					min: <?php echo $min; ?>,
					max: <?php echo $max; ?>
					<?php
						if($placement)
						{
							?>,
								slide: function(event, ui)
								{  $("#<?php echo $placement; ?>").val(ui.value);  }
							<?php
						}
					?>					
				});

				//Move value to input
				<?php
					if($placement)
					{  ?>			$("#<?php echo $placement; ?>").val($("#<?php echo $name; ?>").slider("value"));  <?php }
				?>
			});
		</script>
  <?php
}

#Inserts the jQuery Code to activate tabs on the given name.
function jTabs($name = "tabs")
{
	?>
  	<script language="javascript" type="text/javascript">
			// Tabs
			$('#<?php echo $name; ?>').tabs();
		</script>
  <?php
}

#Creates the jQuery & Javascript needed to initialize data tables script.
function dataTablesCode($name = "", $type = "", $print = TRUE)
{
	switch(strtolower($type))
	{
		case "robust":
			$contents = '
							"bRetrieve": true,
							"bJQueryUI": true,
							"bAutoWidth": false,
							"bFilter": true,
							"bProcessing": true,
							"bLengthChange": true,
							"iDisplayLength": 10,
							"bPaginate": true,
							"sPaginationType": "full_numbers",
							"bSort": true,
							"sDom": \'T<"clear">lfrtip\'';
		break;
		
		case "sort":
			$contents = '
					"bRetrieve": true,
					"bJQueryUI": true,
					"bAutoWidth": false,
					"bFilter": false,
					"bProcessing": true,
					"bLengthChange": true,
					"iDisplayLength": 10,
					"bPaginate": true,
					"sPaginationType": "full_numbers",
					"bSort": true,
					"sDom": \'T<"clear">lfrtip\',
					"oTableTools": { "sSwfPath": "<?php echo $basePath; ?>/scripts/tableTools/swf/ZeroClipboard.swf" }';
		break;
		
		case "simple":
			$contents = '
					"bRetrieve": true,
					"bJQueryUI": true,
					"bAutoWidth": false,
					"bFilter": true,
					"bProcessing": true,
					"bLengthChange": true,
					"iDisplayLength": 10,
					"bPaginate": true,
					"sPaginationType": "full_numbers",
					"bSort": true';
		break;

		default:
			$contents = '
					"bRetrieve": true,
					"bJQueryUI": true,
					"bAutoWidth": false,
					"bFilter": false,
					"bProcessing": true,
					"bLengthChange": true,
					"iDisplayLength": 10,
					"bPaginate": true,
					"sPaginationType": "full_numbers",
					"bSort": false';
		break;
	}
	
  $script = '
		<script language="javascript" type="text/javascript">
      $(document).ready(function(){
        $("#'.$name.'").dataTable({
					'.$contents.'
        });
      });
    </script>'."\n";
	if($print)
	{  echo $script;  }
	else
	{  return $script;  }
}

#Adds the javascript code for datepicker and sets start and end date if needed.
function dateCode($time = FALSE, $minDate = NULL, $maxDate = NULL)
{
	?>
	<script language="javascript" type="text/javascript">
		function datePick(a)
		{
			a = '#' + a;
			$(a).datetimepicker({
        <?php
          if($time)
          {
            ?>
              alwaysSetTime: true,
              showMinute: true,
              showHour: true,
              time: true,
            <?php
          }
          else
          {
            ?>
              alwaysSetTime: false,
              showMinute: false,
              showHour: false,
              time: false,
            <?php
          }
					if(!is_null($minDate))
					{  ?>minDate: new Date(<?php echo date("d, M, Y", strtotime($minDate." - 1 Month")); ?>), <?php  }
					if(!is_null($maxDate))
					{  ?>maxDate: new Date(<?php echo date("d, M, Y", strtotime($maxDate." - 1 Month")); ?>), <?php  }
				?>
				dateFormat: 'yy-m-d'
			});
		}
	</script>
	<?php
}


?>