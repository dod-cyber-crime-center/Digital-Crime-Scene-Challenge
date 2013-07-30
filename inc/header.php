<?php
/*
 * File:        inc/header.php
 * Version:     1.0
 * Description: Standard viewport header for mobile web application
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

  require_once('global.php');  
?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=PAGETITLE?></title>
  <?php  require_once('includes.php');  ?>
</head>
<?php
	switch(strtolower($section))
	{
		case "scoreboard":
			?><body onload="timedRefresh();"><?php
		break;

		default:
			?><body><?php
		break;
	}
?>
<div data-role="page" id="jqm-home" class="type-home" data-title="<?=PAGETITLE?>">
	<div data-role="header">
  	<header>
      <h1><?=strtoupper(PAGETITLE)?></h1>
      <h3><?=$section?></h3>
    </header>
    <a style="float: left;" href="<?php echo $basePath; ?>index.php">HOME</a>
    <a style="float: right;" href="<?php echo $basePath; ?>queue/index.php">In the Queue</a>
  </div>
	<div data-role="content">
<!-- CLOSE IN FOOTER!!! --><!-- CLOSE IN FOOTER!!! --><!-- CLOSE IN FOOTER!!! --><!-- CLOSE IN FOOTER!!! -->