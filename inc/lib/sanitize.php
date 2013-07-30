<?php
/*
 * File:        inc/lib/sanitize.php
 * Version:     1.5
 * Description: Common PHP functions to santize input/outputs for prototype
 *              web application development
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
/*
	Verify a field is truely empty
  RETURNS: TRUE/FALSE
*/
function checkNotEmpty($s)
{  return (trim($s) !== '');  }

/*
	Trim, strip bad html, and safe content
	RETURNS: String
*/
function cleanInput($s)
{  return str_replace("\n","<br>",htmlentities(strip_tags(addslashes(trim($s),'<a><br><p>'))));  }

/* 
	Strip all characters not on the US keyboard and those used for attacks
*/
function sanitizeData($s)
{
	$clean = trim($s); //Take off white space endings
	$clean = urldecode($clean); //Decode if obstruced
	$clean = strip_tags($clean); //Remove the tags
	$clean = preg_replace("/[^A-Za-z0-9 \|\/\.\:\(\)\@\#\&\-\_]/", "", $clean); //Yes, paranoid!
	return trim($clean);
}

/*
	Verify a field is truely empty
	RETURNS: TRUE/FALSE
*/
function isEmpty($s) 
{
  if(strlen($s) == 0) 
	{  return true;  }
  else
  {  return false;  }
}

//Create proper variable names
function makeVarName($passedValue)
{
	for($i = $start; $i < strlen($passedValue); $i++)
	{
		if($passedValue[$i] == "_")
		{  $result .= strtoupper($passedValue[++$i]);  }
		else
		{  $result .= $passedValue[$i];  }
	}
	return $result;
}

$requestVars = array(
	//System values
	"where", "view",
	//Team values
	"teamID", "team_name", "teamInfo", "found",  
	//Player values
	"playerID", "playerInfo", "badge",
	//Item values
	"itemID", "itemInfo"
);

//echo '<pre>$_REQUEST'."\n"; print_r($_REQUEST); echo "\n\nPRINT VARS\n";

foreach($requestVars AS $varName)
{
	//echo "\n\nVARNAME: ".$varName."\n--------------------\n";
  switch($varName)
  {
    case "where":
      $passedValue =  strtolower($_REQUEST[$varName]);
			$newName = makeVarName($varName);
			$$newName = sanitizeData($passedValue);
			//echo "WHERE: ".$newName." = ".sanitizeData($passedValue)."\n";
    break;

    default:
			if(array_key_exists($varName, $_REQUEST) == TRUE)
			{
				unset($tempArray);
				$newName = makeVarName($varName);
				if(is_array($_REQUEST[$varName]))
				{
					foreach($_REQUEST[$varName] as $subName => $subValue)
					{
						if(is_array($subValue))
						{
							foreach($subValue as $name => $value)
							{  $tempArray[$subName][$name] = sanitizeData($value);
								//echo "SUB ARRAY: ".$newName."[".$i."][".$name."] = ".sanitizeData($value)."\n";
							}
						}
						else
						{
							$tempArray[$subName] = sanitizeData($subValue);
							//echo "ARRAY: ".$newName."[".$subName."] = ".sanitizeData($subValue)."\n";
						}
					}
					$$newName = $tempArray;
				}
				else
				{
					$passedValue = $_REQUEST[$varName];
					$$newName = sanitizeData($passedValue);
					//echo "SINGLE: ".$newName." = ".sanitizeData($passedValue)."\n";
				}
			}
			//else # For debug only
			//{  echo "VAR NOT FOUND: ".$varName."\n\n";  } # for debug only
    break;
  }
}

//CLEAR VARIBALES.
unset($varName);
unset($passedValue);
unset($tempString);
unset($tempArray);
unset($subValue);
unset($subName);
unset($value);
unset($name);

//$tempVar = get_defined_vars();
//print_r($tempVar);
//echo "</pre>";
//exit;
?>