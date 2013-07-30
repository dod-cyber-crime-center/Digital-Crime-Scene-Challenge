<?php
/*
 * File:        item/index.php
 * Version:     1.0
 * Description: Item management viewport for the mobile application
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
$section = "Crime Scene Item List";
$pageCSS = "";

require_once('../inc/header.php');

switch($where)
{
	case "edit":
	case "edit item":
		$buttonTitle = "Save";
		displayItemForm("Edit Item Information", "Save", FALSE, $itemID);
	break;

	case "view":
	case "view item":
		$buttonTitle = "Edit";
		displayItemForm("View Item Information", "Edit", TRUE, $itemID);
	break;

	case "add":
	case "add item":
		$itemID = 0;
		$buttonTitle = "Insert";
		displayItemForm("Add New Item", "Insert", FALSE);
	break;

	case "save":
	case "save item":
	case "insert":
	case "insert item":
		if($itemID)
		{  saveItem($itemInfo, $itemID);  }
		else
		{  insertItem($itemInfo);  }
	case "cancel":
	default:
		$sql = "SELECT itemID, item_name, item_type_name FROM view_item_information";
		$stmt = dbQuery($sql);
		dbExecute($stmt);
		$itemList = dbAllRows($stmt, "names");
		if(count($itemList))
		{
			?>
				<div class="ui-body ui-body-a">
					<a data-ajax="false" href="<?php echo $basePath; ?>item/index.php?where=add" data-role="button" data-inline="true" data-theme="b" data-icon="refresh">
						Add Item
					</a>
					<div data-role="fieldcontain">
						<h3>Select an item to edit</h3>
						<ul data-role="listview" data-filter="true" data-inset="true" data-filter-placeholder="Search team..." data-filter-theme="b">
							<?php
								$lastItemTypeName = "";
								foreach($itemList as $rowID => $info)
								{
									$itemID = $info["itemID"];
									$itemName = $info["item_name"];
									$itemTypeName = $info["item_type_name"];
									if($lastItemTypeName != $itemTypeName)
									{
										$lastItemTypeName = $info["item_type_name"];
										?>
											<li data-role="list-divider"><?php echo $itemTypeName; ?></li>
										<?php
									}
									?>
										<li><a data-ajax="false" href="<?php echo $basePath.'item/index.php?where=view&itemID='.$itemID; ?>"><?php echo $itemName; ?></a></li>
									<?php
								}
							?>
						</ul>
					</div>
				</div>
			<?php
		}
		else
		{
			?>
				<h3>No items in list.</h3>
		<a data-ajax="false" href="<?php echo $basePath; ?>item/index.php?where=add" data-role="button" data-inline="true" data-theme="b" data-icon="refresh">Add Item</a>
			<?php
		}
	break;
}
?>
	</div>
<?php
require_once('../inc/footer.php');

function displayItemForm($title = "Item Information", $buttonTitle = "View", $readonly = TRUE, $itemID = 0)
{
	global $basePath;
	global $where;
	
	if($readonly)
	{  $readonly = 'readonly="readonly"';  }

	if($itemID)
	{
		$sql = "SELECT item_name, item_description, item_typeID, point_value, active FROM view_item_list WHERE itemID = :itemID";
		$param[":itemID"] = $itemID;
		$stmt = dbQuery($sql, $param);
		dbExecute($stmt);
		$info = dbAllRows($stmt, "names");
		foreach($info as $row)
		{
			foreach($row as $key => $value)
			{
				$newName = makeVarName($key);
				$$newName = $value;
			}
		}
	}
	
	?>
    <form class="ui-body ui-body-a" method="post" data-ajax="false" action="<?php echo $basePath.'item/index.php?itemID='.$itemID; ?>">
      <h3><?php echo ucwords($title); ?></h3>
      <input type="hidden" name="itemID" value="<?php echo $itemID; ?>" readonly />
      <div data-role="fieldcontain">
        <label for="item_name">Name: </label>
        <input type="text" id="item_name" name="itemInfo[item_name]" value="<?php echo $itemName; ?>" min="3" max="50" <?php echo $readonly; ?> />
      </div>
      <div data-role="fieldcontain">
        <label for="item_description">Description: </label>
        <input type="text" id="item_description" name="itemInfo[item_description]" value="<?php echo $itemDescription; ?>" min="3" max="50" <?php echo $readonly; ?> />
      </div>
      <div data-role="fieldcontain">
        <?php
          if($readonly)
          {
            ?>
              <label for="item_typeID">Type: </label>
              <input type="text" id="item_typeID" name="itemInfo[item_typeID]" value="<?php echo getItemTypeName($itemTypeID); ?>" min="3" max="50" <?php echo $readonly; ?> />
            <?php
          }
          else
          {
            $sql = "SELECT item_typeID, item_type_name FROM view_item_type";
            dropdowns($id = "item_typeID", $name = "itemInfo[item_typeID]", $label = "Type", $type = "select", $itemTypeID, $sql);
          }
        ?>
      </div>
      <div data-role="fieldcontain">
        <label for="point_value">Point Value: </label>
        <input type="text" id="point_value" name="itemInfo[point_value]" value="<?php echo $pointValue; ?>" min="1" max="3" <?php echo $readonly; ?> />
      </div>
      <div data-role="fieldcontain">
        <?php
          if($readonly)
          {
						if($active)
						{  $active = "Yes";  }
						else
						{  $active = "No";  }
            ?>
              <label for="active">Active: </label>
              <input type="text" id="active" name="itemInfo[active]" value="<?php echo $active; ?>" <?php echo $readonly; ?> />
            <?php
          }
          else
          {  dropdowns($id = "active", $name = "itemInfo[active]", $label = "Active", $type = "slider", $active);  }
        ?>
      </div>
      <br /><br />
      <input data-ajax="false" type="submit" name="where" value="<?php echo $buttonTitle; ?> Item" data-inline="true" data-icon="gear" data-theme="e" />
      <input data-ajax="false" type="submit" name="where" value="Cancel" data-inline="true" data-icon="back" />
    </form>
  <?php
}
function saveItem($itemInfo, $itemID)
{
	$fields = "";
	$params[":itemID"] = $itemID;
	foreach($itemInfo as $itemName => $itemValue)
	{
		$fieldName = ":".$itemName;
		$params[$fieldName] = $itemValue;
		$fields .= $fieldName.", ";
	}
	$fields = substr($fields, 0, -2);
	$sql = "CALL sp_save_item_info(:itemID, ".$fields.")";
	$stmt = dbQuery($sql, $params);
	dbExecute($stmt);
	return dbResult($stmt);
}

function insertItem($itemInfo)
{
	$fields = "";
	foreach($itemInfo as $itemName => $itemValue)
	{
		$fieldName = ":".$itemName;
		$params[$fieldName] = $itemValue;
		$fields .= $fieldName.", ";
	}
	$fields = substr($fields, 0, -2);
	$sql = "CALL sp_insert_item(".$fields.")";
	$stmt = dbQuery($sql, $params);
	dbExecute($stmt);
	return dbResult($stmt);
}
?>