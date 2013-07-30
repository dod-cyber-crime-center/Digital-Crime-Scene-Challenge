<?php
/*
 * File:        inc/lib/functions.php
 * Version:     1.5
 * Description: General PHP functions for prototype web application development
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
 
/* DEBUG ARRAY FUNCTION */
//This function takes a title and php var and prints them in a HTML pre block
function DEBUG_ARRAY($title, $passedArray)
{
	echo "<pre style=\"border: 5px solid #090; background-color: #FFF; padding: 5px 10px;\">";
	echo ucwords($title)."\n";
	print_r($passedArray);
	echo "</pre>";
}


#Creates a download dialog for file's data, name, and type provided.
function downloadFile($fileData,$fileName,$fileType)
{
  // Document Types
  $typesArray = array("doc" => "text","txt" => "text","pdf" => "pdf");
  foreach($typesArray AS $matchExt => $replaceFileType)
  {
    if($matchExt == $fileType)
      $finalFileType = $replaceFileType;
  }
  if(empty($finalFileType))
    $finalFileType = "octet-stream";
 
  if($fileData && $fileName && $fileType)
  {
    header("Content-disposition: attachment; filename=$fileName");
    header("Content-type: application/$finalFileType");
    echo $fileData;
  }
}

#Creates a table using the DataTables and TableTools with jQuery styling.
function dataTable($stmt, $tableName = "simple", $tableType = "simple", $url = "", $linkText = "", $linkID = "")
{
	$output = '<table id="'.$tableName.'" class="display" ';
	$output .= ' border="1" cellpadding="2" cellspacing="0">'."\n	<thead>\n		<tr>\n";
	
	//Loop the table fields into an array	and output as a table header
	
	for($i = 0; $i < dbFieldCount($stmt); $i++)
	{
		$fieldName = dbFieldName($stmt, $i);
		if($linkID == $linkText || $linkID != $fieldName)
		{
			$fields[$i] = $fieldName;
			$output .= "		<th>".properNames($fields[$i])."</th>\n";
		}
	}

	$output .= "	</tr>\n		</thead>\n	<tbody>\n";

	//Loop through the rows
	while($row = dbRow($stmt, "names"))
	{
		$output .= '<tr valign="top">'."\n";
		//Extract the data by the table field names
		foreach($fields as $key => $value)
		{
			if(is_numeric($row[$value]))
			{  $align = ' align="right"';  }
			else
			{ $align = "";  }
			
			if($value == $linkText)
			{
				$there = str_replace(" ", "", $linkID);
				$there = str_replace(substr($there, 0, 1), substr(strtolower($there), 0, 1), $there);
				$there = $there.'='.$row[$linkID];
        //echo (strrpos($url, "?") + 1) . " | " . strlen($url);
        //exit;
        if((strrpos($url, "?") + 1) == strlen($url))
        {  $output .= '<td'.$align.'><a href="'.$url.$there.'">'.$row[$value]."</a></td>\n";  }
        else
        {  $output .= '<td'.$align.'><a href="'.$url.'&amp;'.$there.'">'.$row[$value]."</a></td>\n";  }
			}
			elseif($value != $linkID || $linkID == $linkText)
			{  $output .= '<td'.$align.'>'.$row[$value]."</td>\n";  }
		}
		$output .= "</tr>\n";
	}
	$output .= "</tbody>\n</table>\n";
	$output .= dataTablesCode($tableName, $tableType, FALSE);
	return $output;
}

#Converts names to proper style.
function properNames($passedValue)
{
	for($i = $start; $i < strlen($passedValue); $i++)
	{
		if(($passedValue[$i] >= 'A') && ($passedValue[$i] <= 'Z'))
		{
			if($passedValue[$i + 1] != "")
			{
				if(($passedValue[$i + 1] >= 'A') && ($passedValue[$i + 1] <= 'Z'))
				{  $result .= " ".$passedValue[$i];  }
				else
				{  $result .= " ".$passedValue[$i];  }
			}
			else
			{  $result .= $passedValue[$i];  }
		}
		else
		{  $result .= $passedValue[$i];  }
	}
	
	return ucwords(str_replace("_", " ", $result));
}

function formatDate($date)
{  
  if($date)
  {  return date("d-M-Y", strtotime($date));  }
  else
  {  return "";  }
}
?>