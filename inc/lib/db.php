<?php
/*
 * File:        inc/lib/db.php
 * Version:     1.5
 * Description: MySQL PDO database functions for connections and actions
 *              for prototype web application development  
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

/**********************************
        DEBUG INSTRUCTIONS
-----------------------------------
	To activate DEBUG replace all
	"$DEBUG = FALSE;"
	  with
	"GLOBAL $DEBUG;"
	Then change the global to 
		"$DEBUG = TRUE;"	
**********************************/
//Global DEBUG variable.
$DEBUG = FALSE;


//General connection information.
$dbS = "localhost";
$dbN = "cyber_crime_scene";
$dbU = "root";
$dbP = "";

try
{
	$dbh = new PDO("mysql:host=".$dbS.";dbname=".$dbN, $dbU, $dbP);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{  echo $e->getMessage();  }


//Binds, if needed, and prepares the query statement.
function dbQuery($sql, $params = NULL)
{
	global $dbh;
	$DEBUG = FALSE;
	if($DEBUG)
	{
		echo '<fieldset style="color: #000; background-color: #CCC;">'."
						<legend style=\"background-color: #CCC;\">BINDING PARAMETERS</legend>
						<pre>\n<table>\n";
	}

	//Is there parameters to bind?
	if($params)
	{
		if(is_array($params))
		{
			$stmt = $dbh->prepare($sql);
			//Bind parameters
			$count = count($params);
			$keys = array_keys($params);
			if(is_numeric($keys[0]))
			{
				for($i = 1; $i <= $count; $i++)
				{
					$a = $i - 1;
					$stmt->bindParam($i, $params[$a]);
					if($DEBUG)
					{
						echo "<tr>
											<td>BindName: [".$i."] </td>
											<td>&nbsp; &nbsp; &nbsp; </td>
											<td> BindData: [".$params[$a]."]</td>
									</tr>\n";
					}
				}
			}
			else
			{
				foreach($params as $key => $value)
				{
					$stmt->bindParam($key, $params[$key]);
					if($DEBUG)
					{
						echo "<tr>
											<td>BindName: [".$key."] </td>
											<td>&nbsp; &nbsp; &nbsp; </td>
											<td> BindData: [".$params[$key]."]</td>
									</tr>\n";
					}
				}
			}
		}
		else
		{  errorMsg("ERROR BINDING!");  }
	}
	else
	{  $stmt = $dbh->query($sql);  }

	if($DEBUG)
	{  echo "</table>\n</pre>\n</fieldset>\n";  }
	return $stmt;
}

//Returns the executed query object.
function dbExecute(&$stmt)
{
	$stmt->execute();
	dbError($stmt);
}

//Returns a single row from the query statement given.
function dbRow($stmt, $type = "")
{
	switch(strtolower($type))
	{
		case "num":
		case "nums":
		case "number":
		case "numbers":
			return $stmt->fetch(PDO::FETCH_NUM);
		break;

		case "name":
		case "names":
			return $stmt->fetch(PDO::FETCH_ASSOC);
		break;
		
		default:
			return $stmt->fetch(PDO::FETCH_BOTH);
		break;
	}
}

//Return an array of all the results of the given query statement.
function dbAllRows($stmt, $type = "")
{
	switch(strtolower($type))
	{
		case "num":
		case "nums":
		case "number":
		case "numbers":
			return $stmt->fetchAll(PDO::FETCH_NUM);
		break;

		case "name":
		case "names":
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		break;
		
		default:
			return $stmt->fetchAll(PDO::FETCH_BOTH);
		break;
	}
}

//Return the selected (by $position) columns' value.
function dbResult($stmt, $position = 0)
{  return $stmt->fetchColumn($position);  }


//Return the last queries effected row count.
function dbRowCount($stmt)
{  return $stmt->rowCount();  }

#Returns the column count of the given statement.
function dbFieldCount(&$stmt)
{  return $stmt->columnCount();  }

#Returns the column name by numbered position in the given statement.
function dbFieldName(&$stmt, $i = 1)
{
	$meta = $stmt->getColumnMeta($i);
	return $meta["name"];
}

//Returns the last Row ID that was created via the database's auto increment ability.
function dbLastID()
{
	global $dbh;
	return $dbh->lastInsertId();
}

//Print error from database.
function dbError($stmt)
{
	$eCode = $stmt->errorCode();
	if($eCode != 0000)
	{
		echo "Error Code: ".$eCode."<br />";
		foreach($stmt->errorInfo() as $error)
		{  echo "&nbsp;&nbsp;".$error.'<br />';  }
	}
}

//Close Database Connection.
function dbClose()
{
	global $dbh;
	$dbh = NULL;
}

/****************************************************************************
*****************************************************************************
******  DB HISTORY AND GENERAL INSERT, UPDATE, AND DELETE STATEMENTS!  ******
*****************************************************************************
****************************************************************************/

function insertData($info, $tableName, $thisField)
{
	global $dbh;
	//Begin transation.
	$dbh->beginTransaction();
	$DEBUG = FALSE;
	if($DEBUG)
	{
		echo '<fieldset style="background-color: #FFF; color: green;">
						<legend>INSERT BLOCK</legend>';
	}
	try
	{
		//Build Query
		foreach($info as $fieldName => $fieldValue)
		{
			$ksql .= $fieldName.", ";
			$vsql .= "?, ";
			$params[] = $fieldValue;
		}
		$ksql = substr($ksql, 0, -2);
		$vsql = substr($vsql, 0, -2);
		$sql = "INSERT INTO ".$tableName." (".$ksql.") VALUES (".$vsql.")";
		if($DEBUG)
		{
			echo "<pre>PARAMETERS:<br />";
				echo "COUNT: ".count($params)."<br />";
				print_r($params);
			echo "</pre>";
			echo "<pre>QUERY:<br />".$sql."</pre>";
		}
		$stmt = dbQuery($sql, $params);
		dbExecute($stmt);
	
		$recordedID = dbLastID();
		recordInsert($info, $tableName, $thisField, $recordedID);
		$dbh->commit();
	}
	catch(PDOException $err)
	{
		$dbh->rollback();
		errorMsg($sql."<br /><br />".$err->getMessage());
	}

	if($DEBUG)
	{  echo "</fieldset>";  }

	return $recordedID;
}

#Updates data from the info array based in the table (table_name) where the field in the table is "this_field" and equals "thisID"
function updateData($info, $tableName, $thisField, $thisID, $where = "", $whereValue = "")
{
	global $dbh;
	$return = FALSE;
	//Begin transation.
	$dbh->beginTransaction();
	$DEBUG = FALSE;
	if($DEBUG)
	{
		echo '<fieldset style="background-color: #FFF; color: blue;">
						<legend>UPDATE BLOCK</legend>
						Table Name: '.$tableName.'<br />
						Primary Field: '.$thisField.'<br />
						Primary key: '.$thisID.'<br />
						Where: '.$where ." = ".$whereValue.'<br />
						Fields: <pre>';
						print_r($info);
		echo '</pre>';
	}
	try
	{
		$info2 = determineChange($info, $tableName, $thisField, $thisID, $where, $whereValue);
		if($info2)
		{
			foreach($info2 as $fieldName => $fieldValue)
			{
				$ssql .= $fieldName." = ?, ";
				$params[] = $fieldValue;
			}
			$params[] = $thisID;
			$sql = "UPDATE ".$tableName." SET ".substr($ssql, 0, - 2)." WHERE ".$thisField." = ?";
			if($where && $whereValue)
			{
				$sql .= " AND ".$where." ?";
				$params[] = $whereValue;
			}
			if($DEBUG)
			{
				echo "<pre>QUERY:<br />".$sql."</pre>";
				echo "<pre>PARAMETERS:<br />"; print_r($params); echo "</pre>";
			}
			$stmt = dbQuery($sql, $params);
			dbExecute($stmt);
			$dbh->commit();
			$return = $thisID;
		}
		else
		{  infoMsg("There was NO change in the data to update!");  }
		
		if($DEBUG)
		{  echo "</fieldset>";  }
	}
	catch(PDOException $err)
	{
		$dbh->rollback();
		errorMsg($sql."<br /><br />".$err->getMessage());
	}

	if($DEBUG)
	{  echo "</fieldset>";  }
	return $return;
}


#Delete data from database.
function deleteData($tableName, $thisField, $thisID, $where = "", $whereValue = "")
{
	$DEBUG = TRUE;
	global $dbh;
	$return = FALSE;
	$dbh->beginTransaction();
	
	if($DEBUG)
	{
		echo '<fieldset style="background-color: #FFF; color: #F00; border-color: #F00;">
						<legend>DELETE BLOCK</legend>
						Table Name: '.$tableName.'<br />
						Primary Field: '.$thisField.'<br />
						Primary key: '.$thisID.'<br />
						Where: '.$where ." = ".$whereValue.'<br />';
	}

	try
	{
		$recorded = addDeleteHistory($tableName, $thisField, $thisID, $where, $whereValue);
		if($recorded)
		{
			$params = array($thisID);
			$sql = "DELETE FROM ".$tableName." WHERE ".$thisField." = ? ";
			if($where)
			{
				$sql .= " AND ?";
				$params[] = $whereValue;
			}
			if($DEBUG)
			{
				echo "<pre>QUERY:<br />".$sql."</pre>";
				echo "<pre>PARAMETERS:<br />"; print_r($params); echo "</pre>";
			}
			$stmt = dbQuery($sql, $params);
			dbExecute($stmt);
			$return = TRUE;
			$dbh->commit();
		}
		else
		{
			$dbh->rollback();
			errorMsg("The database was unable to record the data being deleted.  Delete transaction failed.");
		}
	}
	catch(PDOException $err)
	{
		$dbh->rollback();
		errorMsg($sql."<br /><br />".$err->getMessage());
	}

	if($DEBUG)
	{  echo "</fieldset>";  }
	return $return;
}

#This function records when data is Truely deleted from the database.  
#This should only occur on the tables in the aurthorizedTables Array.
function addDeleteHistory($tableName, $thisField, $thisID, $where, $whereValue)
{
	$DEBUG = FALSE;
	$return = FALSE;
	$authorizedTables = array("methods");
	
	//If table name in allowable delete tables.
	if(in_array($tableName, $authorizedTables) == true)
	{
		if($DEBUG)
		{
			echo '<fieldset style="background-color: #FFF; color: #F00;">
							<legend>WHAT TO DELETE</legend>
							Table Name: '.$tableName.'<br />
							Primary Field: '.$thisField.'<br />
							Primary key: '.$thisID.'<br />
							Where: '.$where ." = ".$whereValue.'<br />';
		}
		
		//Build Query
		$sql = "SELECT * FROM ".$tableName." WHERE ".$thisField.' = ? ';
		$params = array($thisID);
		if($where && $whereValue)
		{
			$sql .= ' AND '.$where. " = ?";
			$params[] = $whereValue;
		}
		if($DEBUG)
		{
			echo "<pre>QUERY:<br />".$sql."</pre>";
			echo "<pre>PARAMETERS:<br />"; print_r($params); echo "</pre>";
		}
		$stmt = dbQuery($sql, $params);
		dbExecute($stmt);
		if(dbRowCount($stmt) > 0)
		{
			$show = dbRow($stmt, "names");
			if($DEBUG)
			{
				echo "<pre>What will be deleted: <br />";
				print_r($show);
				echo "</pre>";
			}
			foreach($show as $key => $value)
			{  $return = logChange($tableName, $key, $value, "DELETED", $thisField, $thisID, "Delete");  }
		}
	}
	
	if($DEBUG)
	{  echo "</fieldset>";  }
	return $return;
}

#This function will find if there is a change to a given table.
function determineChange($update, $tableName, $thisField, $thisID, $where = "", $whereValue = "")
{
	$DEBUG = FALSE;
	
	#find possible fields of change from database.
	foreach($update as $key => $value)
	{  $selectedFields .= $key.", ";  }
	
	$selectedFields = substr($selectedFields, 0, - 2);
	$sql = "SELECT ".$selectedFields." FROM ".$tableName." WHERE ".$thisField." = ? ";
	$params = array($thisID);
	if($where && $whereValue)
	{
		$sql .= " AND ".$where." = ?";
		$params[] = $whereValue;
	}
	if($DEBUG)
	{
		echo '<fieldset style="background-color: #FFF; color: #000;">
						<legend>PRE DETERMINED CHANGE: </legend>';
		echo "<pre>QUERY:<br />".$sql."</pre>";
		echo "<pre>PARAMETERS: <br />";
		print_r($params);
		echo '</pre>';
	}
	$stmt = dbQuery($sql, $params);
	dbExecute($stmt);
	$allRows = dbAllRows($stmt, "names");
	foreach($allRows as $row)
	{  $current = $row;  }

	if($DEBUG)
	{
		echo "Compare Arrays:<br /><pre>Current In Database:<br />";
		print_r($current);
		echo "<br />To be Updated with: <br />";
		print_r($update);
		echo "</pre>";
	}
	
	$change = FALSE;
	$logDate = date("Y-m-d H:i:s");
	#Compare fields values to see if change occured.
	if($current)
	{
		foreach($current as $key => $value)
		{
			if(array_key_exists($key, $update) == true)
			{
				if($value != $update[$key])
				{
					$change = TRUE;
					$status = logChange($tableName, $key, $value, $update[$key], $thisField, $thisID, "Update");
					$changeValues[$key] = $update[$key];
					if($DEBUG)
					{
						echo '<fieldset style="background-color: #FFF; color: #000;">
										<legend>DETERMINED CHANGE: </legend>
										<ul>
											<li>Date Logged: '.$logDate.'</li>
											<li>Table Moded: '.$tableName.'</li>
											<li>Field of Change: '.$key.'</li>
											<li>Before Value: NULL</li>
											<li>New Value: '.$value.'</li>
											<li>Primary Key Field: '.$thisField.'</li>
											<li>Primary Key Value: '.$thisID.'</li>
										</ul>
									</fieldset>';
					}
				}
			}
		}
	}
	else
	{  errorMsg("Nothing selected to compare!");  }

	if($change)
	{  return $changeValues;  }
	else
	{  return false;  }
}


//This function will setup the log of the insert to the give table.
function recordInsert($info, $tableName, $thisField, $thisID)
{
	$DEBUG = FALSE;
	$status = FALSE;
	foreach($info as $key => $value)
	{
		if($DEBUG)
		{
			echo '<fieldset style="background-color: #FFF; color: #000;">
							<legend>RECORD INSERT: </legend>
							<ul>
								<li>Date Logged: '.date("Y-m-d H:i:s").'</li>
								<li>Table Moded: '.$tableName.'</li>
								<li>Field of Change: '.$key.'</li>
								<li>Before Value: NULL</li>
								<li>New Value: '.$value.'</li>
								<li>Primary Key Field: '.$thisField.'</li>
								<li>Primary Key Value: '.$thisID.'</li>
							</ul>
						</fieldset>';
		}
		$status = logChange($tableName, $key, NULL, $value, $thisField, $thisID, "Insert");
	}
	return $status;
}

//This function will insert the data changing into the history table from the given table.
function logChange($tableName, $fieldName, $before, $after, $primaryKeyField, $primaryKeyValue, $logType)
{
	$status = FALSE;
	$DEBUG = FALSE;
	//Build Query
	$logDate = date("Y-m-d H:i:s");
	$user = whoChanged();
	
	$params = array($fieldName, $before, $after, $user, 
									$primaryKeyField, $primaryKeyValue);
	try
	{
		$logSQL = 'INSERT INTO history 
								(created, table_name, field_name, before_change, after_change, user_name, primary_key_field, primary_key_value, log_type)
								VALUES 
								("'.$logDate.'", "'.$tableName.'", ?, ?, 
								 	?, ?, ?, ?, "'.$logType.'")';
		if($DEBUG)
		{
			echo '<fieldset style="background-color: #FFF; color: #000;">
							<legend>PRE LOG CHANGE: </legend>';
			echo "<pre>QUERY:<br />".$logSQL."</pre>";
			echo "<pre>PARAMETERS: <br />";
			print_r($params);
			echo '</pre>
				</fieldset>';
		}
		$stmt = dbQuery($logSQL, $params);
		dbExecute($stmt);
		$status = TRUE;
		
	}
	catch(PDOExecption $err)
	{
		dbError($stmt);
		$status = FALSE;
	}
	if($DEBUG)
	{
		echo '<fieldset style="background-color: #FFF; color: #000;">
						<legend>POST LOG CHANGE: </legend>';
		echo "<pre>QUERY:<br />".$logSQL."</pre>";
		echo "<pre>PARAMETERS: <br />";
		print_r($params);
		echo '</pre>
			</fieldset>';
	}
	return $status;
}

#This function will record who made a database change.
function whoChanged()
{
	$name = $_SESSION[last_name]. ", ".$_SESSION[first_name];
	if($name != ", ")
	{  return $name;  }
	else
	{  return "DEV USER";  }
}
?>