<?php
/*
 * File:        scoreboard/scorecard.php
 * Version:     1.0
 * Description: Team scorecard viewport for the mobile application
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
 
$sql = "SELECT disqualified 
				FROM view_team_info 
				WHERE teamID = :teamID";
$param[":teamID"] = $teamID;
$stmt = dbQuery($sql, $param);
dbExecute($stmt);
$disqualified = dbResult($stmt);
if(!$disqualified)
{
	$sql = "SELECT vit.item_type_name, vil.itemID, vil.item_name, vil.point_value, 
					IFNULL((
						SELECT vfl.correct_value 
						FROM view_found_list vfl 
						WHERE vfl.itemID = vil.itemID 
							AND vfl.teamID = :teamID
					), 0) as correct_value,
					IFNULL((
						SELECT vfl.incorrect_value 
						FROM view_found_list vfl 
						WHERE vfl.itemID = vil.itemID 
							AND vfl.teamID = :teamID
					), 0) as incorrect_value
					FROM view_item_list vil 
					INNER JOIN view_item_type vit ON vit.item_typeID = vil.item_typeID 
					ORDER BY vil.item_typeID, vil.item_name, vil.point_value";
	$stmt = dbQuery($sql, $param);
	dbExecute($stmt);
	while($row = dbRow($stmt, "names"))
	{
		$allFound[$row["itemID"]]["item_type_name"] = $row["item_type_name"];
		$allFound[$row["itemID"]]["item_name"] = $row["item_name"];
		$allFound[$row["itemID"]]["point_value"] = $row["point_value"];
		$allFound[$row["itemID"]]["correct_value"] = $row["correct_value"];
		$allFound[$row["itemID"]]["incorrect_value"] = $row["incorrect_value"];
	}
	if(count($allFound))
	{
		?>
			<fieldset class="ui-body-b ui-corner-bottom ui-corner-right teamcard">
				<legend class="ui-body-b ui-corner-top">
					<?php echo getTeamName($teamID); ?> (<em><small><?php echo getScenarioType($teamID); ?></small></em>)
				</legend>
				<div>
					<?php
						if($admin)
						{
							?>
								<table class="ui-body-c ui-corner-all padded this" cellspacing="0">
									<?php
									if(count($allFound) > 0 && is_array($allFound))
									{
										$lastItemTypeName = "";
										foreach($allFound as $item => $itemData)
										{
											##  Variables for columns
											$itemTypeName = $itemData["item_type_name"];
											$itemName = $itemData["item_name"];
											$point = $itemData["point_value"];
											$correct = $itemData["correct_value"] ;
											$incorrect = $itemData["incorrect_value"];
											$lineTotal = $correct + $incorrect;

											##  Variables for summary of columns
											$score += $lineTotal;
											switch(strtolower($itemTypeName))
											{
												case 'attempt bonus': 
												case 'evidence':
													$sum += $point;
												case 'improper behavior':
													if($lineTotal <= 0)
													{  $class = 'class="missed"';  }
													else
													{  $class = 'class="got"';  }
		
													if($correct > 0)
													{  $correct = "Yes";  }
													else
													{  $correct = " - ";  }
		
													if($incorrect > 0)
													{  $incorrect = " - ";  }
													else
													{  $incorrect = " - ";  }
												break;

												default: 
													{
														$sum += $point;
														if($lineTotal <= 0)
														{  $class = 'class="missed"';  }
														else
														{  $class = 'class="got"';  }
			
														if($correct > 0)
														{  $correct = "YES";  }
														else
														{  $correct = "NO";  }
			
														if($correct == "YES")
														{
															if($incorrect > 0)
															{  $incorrect = "YES";  }
															else
															{  $incorrect = "NO";  }
														}
														else
														{  $incorrect = "-";  }
													}
												break;
											}

											if(empty($lastItemTypeName) || $lastItemTypeName != $itemTypeName)
											{
												$lastItemTypeName = $itemTypeName;
												?>
                          <thead>
                            <tr>
                              <th class="title" colspan="5"><?=$itemTypeName?></th>
                            </tr>
                            <tr>
                              <th align="right">NAME</th>
                              <th align="right">VALUE</th>
                              <th align="right">FOUND</th>
                              <th align="right">TRIED</th>
                              <th align="right">EARNED</th>
                            </tr>
                          </thead>
                        <?php
											}

											echo '<tr>
													<td>'.$itemName.': </td>
													<td>'.$point."</td>
													<td ".$class." >".$correct."</td>
													<td ".$class." >".$incorrect."</td>
													<td ".$class." >".$lineTotal."</td>
												</tr>";
										}
									}
									echo '<tr class="sum">
										<td>TOTAL: </td>
										<td><b>'.$sum.'</b></td>
										<td colspan="2"></td>
										<td><b>'.$score.'</b></td></tr>';
								?>
								</table>
								<p>Completed investigation in <b>(<?php echo getTimeTaken($teamID); ?>)</b> for a Total Score: <b><?php echo $score; ?></b></p>
							<?php
						}
						else
						{
							foreach($allFound as $item => $itemData)
							{  $sum += $itemData["correct_value"] + $itemData["incorrect_value"];   }
							echo "Completed investigation in <b>(".getTimeTaken($teamID).")</b> for a <b>Total Score: ".$sum."</b>";
						}
					?>
				</div>
				<br />	
			</fieldset>
		<?php
	}
	else
	{
		?>
			<h3><?php echo getTeamName($teamID); ?></h3>
			<p>Team has not participated at this time.</p>
			<a data-role="button" data-inline="true" data-ajax="false" data-theme="b" href="<?php echo $basePath; ?>scoreboard/team_score.php">Back to Team List</a>
			<a data-role="button" data-inline="true" data-ajax="false" data-theme="e" href="<?php echo $basePath; ?>queue/item_list.php?teamID=<?php echo $teamID; ?>">Play Now</a>
		<?php
	}
}
else
{
	?>
		<h3><?php echo getTeamName($teamID); ?></h3>
		<p>Team has been disqualified.</p>
		<a data-role="button" data-inline="true" data-ajax="false" data-theme="b" href="<?php echo $basePath; ?>scoreboard/team_score.php">Back to Team List</a>
		<a data-role="button" data-inline="true" data-ajax="false" data-theme="e" href="<?php echo $basePath; ?>team/team.php?where=view&teamID=<?php echo $teamID; ?>">View Team</a>
	<?php
}