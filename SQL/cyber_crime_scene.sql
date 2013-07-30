--
-- Database: 'cyber_crime_scene'
--

-- --------------------------------------------------------

--
-- Create the database 'cyber_crime_scene'
--

CREATE DATABASE cyber_crime_scene DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE USER 'ccs_user'@'localhost' IDENTIFIED BY  'S0m3_ccs_us3r_pw!!!';

GRANT SELECT , INSERT , UPDATE , DELETE , EXECUTE ON  `cyber\_crime\_scene` . * TO  'ccs_user'@'localhost';

--
-- Database: 'cyber_crime_scene'
--

-- --------------------------------------------------------

--
-- Table structure for table 'found'
--

CREATE TABLE cyber_crime_scene.found 
(
  itemID int(11) NOT NULL,
  teamID int(11) NOT NULL,
	time_found timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	wrong tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY found (itemID,teamID,time_found,wrong)
);

-- --------------------------------------------------------

--
-- Table structure for table 'item'
--

CREATE TABLE cyber_crime_scene.item 
(
  itemID int(11) NOT NULL AUTO_INCREMENT,
  item_name varchar(50) NOT NULL,
	item_description varchar(150) DEFAULT NULL,
  item_typeID int(11) NOT NULL,
  point_value int(11) NOT NULL,
  active int(11) NOT NULL,
  PRIMARY KEY (itemID),
  KEY itemID (itemID,item_name,item_typeID,point_value,active)
);

-- --------------------------------------------------------



--
-- Table structure for table 'item_type'
--

CREATE TABLE cyber_crime_scene.item_type 
(
  item_typeID int(11) NOT NULL AUTO_INCREMENT,
  item_type_name varchar(75) NOT NULL,
  PRIMARY KEY (item_typeID),
  KEY item_typeID (item_typeID,item_type_name)
);

-- --------------------------------------------------------

--
-- Table structure for table 'player'
--

CREATE TABLE cyber_crime_scene.player 
(
  playerID int(11) NOT NULL AUTO_INCREMENT,
  badgeID varchar(250) NOT NULL,
  first_name varchar(40) NOT NULL,
  last_name varchar(40) NOT NULL,
  active tinyint(1) NOT NULL DEFAULT '1',
  created_date datetime DEFAULT NULL,
  modified_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (playerID),
  UNIQUE KEY badgeID_2 (badgeID),
  KEY playerID (playerID,badgeID,active)
);

-- --------------------------------------------------------

--
-- Table structure for table 'team'
--

CREATE TABLE cyber_crime_scene.team 
(
  teamID int(11) NOT NULL AUTO_INCREMENT,
  team_name varchar(75) NOT NULL,
	scenario_type int(11) DEFAULT '1',
  disqualified tinyint(1) NOT NULL DEFAULT '0',
  start_time datetime DEFAULT NULL,
  attempt_time int(11) DEFAULT NULL,
  points int(11) DEFAULT NULL,
  created_date datetime DEFAULT NULL,
  modified_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (teamID),
  KEY disqualified (disqualified,start_time,attempt_time,points)
);

-- --------------------------------------------------------

--
-- Table structure for table 'scenario'
--

CREATE TABLE cyber_crime_scene.scenario 
(
  scenarioID int(11) NOT NULL AUTO_INCREMENT,
  scenario_name varchar(75) NOT NULL,
  UNIQUE KEY scenarioID (scenarioID),
  KEY scenarioType (scenarioID,scenario_name)
);

-- --------------------------------------------------------

--
-- Table structure for table 'team_members'
--

CREATE TABLE cyber_crime_scene.team_members 
(
  playerID int(11) NOT NULL,
  teamID int(11) NOT NULL,
  UNIQUE KEY playerID (playerID),
  KEY teamID (teamID)
);

-- --------------------------------------------------------

--
-- Table structure for table 'menu'
--

CREATE TABLE cyber_crime_scene.menu 
(
  menuID int(11) NOT NULL AUTO_INCREMENT, 
  menu_name varchar(50) NOT NULL, 
	menu_link varchar (75) DEFAULT NULL, 
	roleID int(11) NOT NULL DEFAULT '0', 
  PRIMARY KEY menuID (menuID)
);

-- --------------------------------------------------------

--
-- Table structure for table 'menu_item'
--

CREATE TABLE cyber_crime_scene.menu_item 
(
  menu_itemID int(11) NOT NULL AUTO_INCREMENT, 
  menu_item_name varchar(50) NOT NULL, 
	menu_item_link varchar (75) NOT NULL, 
	roleID int(11) NOT NULL DEFAULT '0',  
  PRIMARY KEY menu_itemID (menu_itemID)
);

-- --------------------------------------------------------

--
-- Table structure for table 'role'
--

CREATE TABLE cyber_crime_scene.role 
(
  roleID int(11) NOT NULL AUTO_INCREMENT, 
  role_name varchar(50) NOT NULL, 
	role_type int(11), 
  PRIMARY KEY roleID (roleID)
);

-- --------------------------------------------------------

--
-- Database: 'cyber_crime_scene'
-- 
-- --------------------------------------------------------

--
-- View structure for view 'view_team_member_count'
--

DROP VIEW IF EXISTS view_team_info;

CREATE VIEW cyber_crime_scene.view_team_info
(
  teamID, 
	team_name,
	scenario_type, 
	score, 
	attempt_time, 
	disqualified 
)
AS
SELECT t.teamID, t.team_name, t.scenario_type, t.points, 
	t.attempt_time,	t.disqualified 
  FROM team t 
	ORDER BY t.points, t.attempt_time;

-- --------------------------------------------------------

--
-- View structure for view 'view_team_member_count'
--

DROP VIEW IF EXISTS view_team_member_count;

CREATE VIEW cyber_crime_scene.view_team_member_count
(
	teamID,
  team_name,
  player_count
)
AS
SELECT t.teamID, t.team_name, IFNULL(count(tm.playerID), 0) as player_count 
  FROM team t 
  LEFT JOIN team_members tm ON tm.teamID = t.teamID 
  GROUP BY t.team_name;

-- --------------------------------------------------------

--
-- View structure for view 'view_team_member_count'
--

DROP VIEW IF EXISTS view_player_info;

CREATE VIEW cyber_crime_scene.view_player_info
(
	playerID,
	badgeID,
  first_name,
	last_name,
	active,
	created_date,
	modified_date
)
AS
SELECT playerID, badgeID, first_name, last_name, active, created_date, modified_date  
  FROM player;

-- --------------------------------------------------------

--
-- View structure for view 'view_team_members'
--

DROP VIEW IF EXISTS view_team_members;

CREATE VIEW cyber_crime_scene.view_team_members
(
	teamID,
	playerID,
	player_name
)
AS
SELECT tm.teamID, p.playerID, CONCAT(p.last_name, ', ', p.first_name) player_name 
  FROM team_members tm 
  INNER JOIN player p ON p.playerID = tm.playerID 
  GROUP BY tm.teamID, p.playerID;

-- --------------------------------------------------------

-- 
-- View of teams in Queue
-- 

DROP VIEW IF EXISTS view_queue;

CREATE VIEW cyber_crime_scene.view_queue 
( 
	scenario,
	teamID, 
	team_name, 
	player_name 
) 
AS 
SELECT s.scenario_name, t.teamID, t.team_name, CONCAT(p.last_name, ', ', p.first_name) as player_name
	FROM team t 
	LEFT JOIN scenario s ON s.scenarioID = t.scenario_type 
	LEFT JOIN team_members tm ON t.teamID = tm.teamID 
	INNER JOIN player p ON p.playerID = tm.playerID AND p.active = 1 
	WHERE t.disqualified = 0 
		AND t.start_time IS NULL 
		AND t.attempt_time IS NULL 
		AND t.points IS NULL 
	GROUP BY scenario_name, t.teamID, player_name 
	ORDER BY scenario_name, t.created_date, t.teamID;
	
-- --------------------------------------------------------

-- 
-- View of item list 
-- 

DROP VIEW IF EXISTS cyber_crime_scene.view_item_list;

CREATE VIEW cyber_crime_scene.view_item_list 
( 
	itemID,
	item_name,
	item_description,
	item_typeID,
	point_value,
	active
) 
AS 
SELECT itemID, item_name, item_description, item_typeID, point_value, active  
	FROM item 
	ORDER BY item_typeID, item_name, point_value;

-- --------------------------------------------------------

-- 
-- View of found list 
-- 

DROP VIEW IF EXISTS cyber_crime_scene.view_found_list;

CREATE VIEW cyber_crime_scene.view_found_list 
( 
	teamID, 
	itemID, 
	item_name, 
	item_typeID, 
	correct_value,
	incorrect_value,
	time_found  
) 
AS 
SELECT t.teamID, i.itemID, i.item_name, i.item_typeID, 
	i.point_value as correct_value, 
	(f.wrong * -1) as incorrect_value, 
	f.time_found 
	FROM team t 
	LEFT JOIN found f ON t.teamID = f.teamID 
	INNER JOIN item i ON i.itemID = f.itemID 
	WHERE t.disqualified = 0;

-- --------------------------------------------------------

-- 
-- View of all teams
-- 

DROP VIEW IF EXISTS view_teams;

CREATE VIEW cyber_crime_scene.view_teams 
( 
	teamID, 
	team_name, 
	player_name 
) 
AS 
SELECT t.teamID, t.team_name, CONCAT(p.first_name, ', ', p.last_name) as player_name
	FROM team t 
	LEFT JOIN team_members tm ON t.teamID = tm.teamID 
	INNER JOIN player p ON p.playerID = tm.playerID 
	GROUP BY t.teamID, player_name 
	ORDER BY t.created_date, t.teamID;
	
-- --------------------------------------------------------

-- 
-- View of all teams
-- 

DROP VIEW IF EXISTS view_team_score;

CREATE VIEW cyber_crime_scene.view_team_score 
( 
	teamID, 
	team_name, 
	scenario_name, 
	attempt_time, 
	points 
) 
AS 
SELECT vti.teamID, vti.team_name, vti.scenario_type, vti.attempt_time, 
	(
		SELECT SUM((correct_value + incorrect_value))
		FROM view_found_list vfl 
		WHERE vfl.teamID = vti.teamID 
	) as points
	FROM view_team_info vti 
	ORDER BY vti.attempt_time, points, vti.teamID;
	
-- --------------------------------------------------------

-- 
-- View of team scores and ranks
-- 

DROP VIEW IF EXISTS view_scoreboard;

CREATE VIEW cyber_crime_scene.view_scoreboard 
( 
	team_name, 
	attempt_time, 
	points, 
	scenario_type
) 
AS 
SELECT vts.team_name, vts.attempt_time, vts.points, t.scenario_type 
	FROM view_team_score vts 
	INNER JOIN team t ON vts.teamID = t.teamID 
	WHERE vts.points IS NOT NULL 
	ORDER BY vts.points DESC, vts.attempt_time DESC, t.created_date ASC;

-- --------------------------------------------------------

-- 
-- View of scenario infomation
-- 

DROP VIEW IF EXISTS view_scenario;

CREATE VIEW cyber_crime_scene.view_scenario 
( 
	scenarioID, 
	scenario_name
) 
AS 
SELECT scenarioID, scenario_name
	FROM scenario
	ORDER BY scenario_name;

-- --------------------------------------------------------

-- 
-- View of item infomation
-- 

DROP VIEW IF EXISTS view_item_information;

CREATE VIEW cyber_crime_scene.view_item_information 
( 
	itemID, 
	item_name,
	item_type_name	
) 
AS 
SELECT i.itemID, i.item_name, item_type_name 
	FROM item i 
	INNER JOIN item_type it ON it.item_typeID = i.item_typeID 
	GROUP BY it.item_typeID, item_name 
	ORDER BY it.item_typeID, i.item_name;

-- --------------------------------------------------------

-- 
-- View of item type
-- 

DROP VIEW IF EXISTS view_item_type;

CREATE VIEW cyber_crime_scene.view_item_type 
( 
	item_typeID, 
	item_type_name 
) 
AS 
SELECT item_typeID, item_type_name 
	FROM item_type 
	ORDER BY item_type_name;

-- --------------------------------------------------------

--
-- Database: 'cyber_crime_scene'
--

-- --------------------------------------------------------

--
-- Procedure for adding and updating a team.
--

DROP PROCEDURE IF EXISTS sp_create_team;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_create_team 
( 
	IN p_teamID int(11), 
	IN p_team_name varchar(75),
	IN p_disqualified tinyint(1),
	IN p_scenario_type int(11)
) 
BEGIN 
	DECLARE out_teamID int;
	IF (p_teamID = 0) THEN 
		INSERT INTO team (team_name, scenario_type, created_date) VALUES (p_team_name, p_scenario_type, CURDATE()); 
		SELECT MAX(teamID) INTO out_teamID FROM team WHERE team_name LIKE p_team_name LIMIT 1;
	ELSE 
		UPDATE team 
			SET team_name = p_team_name 
				AND disqualified = p_disqualified 
				AND scenario_type = p_scenario_type 
		WHERE teamID = p_teamID; 
		SET out_teamID = p_teamID;
	END IF; 
	SELECT out_teamID;
END //

DELIMITER ;

-- --------------------------------------------------------

-- 
-- Procedure for adding and updating a player.
-- 

DROP PROCEDURE IF EXISTS sp_save_player;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_save_player 
( 
	IN p_playerID int(11),
	IN p_badgeID varchar(250),
	IN p_first_name varchar(40),
	IN p_last_name varchar(40),
	IN p_active tinyint(1)
) 
BEGIN 
	DECLARE out_playerID int;
	DECLARE temp_badge varchar(250);
	SELECT IFNULL(badgeID, 0) INTO temp_badge FROM player WHERE badgeID LIKE p_badgeID;
	IF (p_playerID = 0 OR temp_badge <> 0) THEN 
		INSERT INTO player (badgeID, first_name, last_name, active, created_date) 
			VALUES (p_badgeID, p_first_name, p_last_name, p_active, CURDATE()); 
		SELECT MAX(playerID) INTO out_playerID FROM player WHERE badgeID LIKE p_badgeID LIMIT 1;
	ELSE 
		UPDATE player SET 
			badgeID = p_badgeID,
			first_name = p_first_name, 
			last_name = p_last_name, 
			active = p_active
		WHERE playerID = p_playerID; 
		SET out_playerID = p_playerID;
	END IF; 
	SELECT out_playerID;
END //

DELIMITER ;

-- --------------------------------------------------------

-- 
-- Procedure for adding and updating a player.
-- 

DROP PROCEDURE IF EXISTS sp_add_team_member;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_add_team_member 
( 
	IN p_teamID int(11), 
	IN p_playerID int(11)
) 
BEGIN 
	INSERT INTO team_members (teamID, playerID) VALUES (p_teamID, p_playerID);
END //

DELIMITER ;

-- --------------------------------------------------------

-- 
-- Procedure for add/update item found by a team.
-- 

DROP PROCEDURE IF EXISTS sp_found_item;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_found_item 
( 
	IN p_itemID int(11),
	IN p_teamID int(11), 
	IN p_time_found datetime, 
	IN p_wrong tinyint(1)
) 
BEGIN 
	INSERT INTO found (itemID, teamID, time_found, wrong) VALUES (p_itemID, p_teamID, p_time_found, p_wrong);
END //

DELIMITER ;

-- --------------------------------------------------------

-- 
-- Procedure for calculating a teams score.
-- 

DROP PROCEDURE IF EXISTS sp_calc_team_score;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_calc_team_score 
( 
	IN p_teamID int(11),
	IN p_start_time datetime, 
	IN p_attempt_time int(11)
) 
BEGIN 
	DECLARE v_points int(11);
	SELECT points INTO v_points FROM view_team_score WHERE teamID = p_teamID;
	UPDATE team SET start_time = p_start_time, attempt_time = p_attempt_time, points = v_points WHERE teamID = p_teamID;
END //

DELIMITER ;

-- --------------------------------------------------------

-- 
-- Procedure for disqualifing a team.
-- 

DROP PROCEDURE IF EXISTS sp_disqualify_team;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_disqualify_team 
( 
	IN p_teamID int(11)
) 
BEGIN 
	UPDATE team SET 
		disqualified = 1, 
		start_time = NULL, 
		attempt_time = NULL, 
		points = NULL 
	WHERE teamID = p_teamID;
END //

DELIMITER ;

-- --------------------------------------------------------

-- 
-- Procedure for save team's information.
-- 

DROP PROCEDURE IF EXISTS sp_save_team_info;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_save_team_info 
( 
	IN p_teamID int(11),
	IN p_team_name varchar(75),
	IN p_disqualified tinyint(1),
	IN p_scenario_type int(11)
) 
BEGIN 
	DECLARE out_teamID int;
	UPDATE team SET 
		disqualified = p_disqualified, 
		team_name = p_team_name, 
		scenario_type = p_scenario_type 
	WHERE teamID = p_teamID;
	SET out_teamID = p_teamID;	
	SELECT out_teamID;
END //

DELIMITER ;

-- --------------------------------------------------------

-- 
-- Procedure for insert item's information.
-- 

DROP PROCEDURE IF EXISTS sp_insert_item;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_insert_item 
( 
	IN p_item_name varchar(50),
	IN p_item_description varchar(75),
	IN p_item_typeID int(11),
	IN p_point_value int(11),
	IN p_active int(11)
) 
BEGIN 
	INSERT INTO item (item_name, item_description, item_typeID, point_value, active)
		VALUES (p_item_name, p_item_description, p_item_typeID, p_point_value, p_active);
	SELECT MAX(itemID) FROM item;
END //

DELIMITER ;

-- --------------------------------------------------------

-- 
-- Procedure for save item's information.
-- 

DROP PROCEDURE IF EXISTS sp_save_item_info;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_save_item_info 
( 
	IN p_itemID int(11),
	IN p_item_name varchar(50),
	IN p_item_description varchar(75),
	IN p_item_typeID int(11),
	IN p_point_value int(11),
	IN p_active int(11)
) 
BEGIN 
	DECLARE out_itemID int;
	UPDATE item SET 
		item_name = p_item_name,
		item_description = p_item_description, 
		item_typeID = p_item_typeID,
		point_value = p_point_value,
		active = p_active	
	WHERE itemID = p_itemID;
	SET out_itemID = p_itemID;
	SELECT out_itemID;
END //

DELIMITER ;

-- --------------------------------------------------------

-- 
-- Procedure for change the player's team.
-- 

DROP PROCEDURE IF EXISTS sp_change_teams;

DELIMITER //

CREATE PROCEDURE cyber_crime_scene.sp_change_teams
( 
	IN p_playerID int(11),
	IN p_teamID int(11)
) 
BEGIN 
	DECLARE out_teamID int;
	DELETE FROM team_members 
			WHERE playerID = p_playerID;
	IF (p_teamID > 0) THEN
		INSERT INTO team_members (playerID, teamID)
			VALUES(p_playerID, p_teamID);
	END IF;
	SET out_teamID = p_teamID;
	SELECT out_teamID;
END //

DELIMITER ;


-- --------------------------------------------------------

-- 
-- Show a list of all the procedures in the database.
-- 

-- SHOW PROCEDURE STATUS;

-- --------------------------------------------------------

-- 
-- Show a list of all the procedures in the database with specific information from the schema.
-- 

-- SELECT `ROUTINE_NAME`, `ROUTINE_DEFINITION` FROM information_schema.routines;

--
-- Database: 'cyber_crime_scene'
--

-- --------------------------------------------------------

--
-- Data to be inserted into 'item'
--

INSERT INTO cyber_crime_scene.item 
	(item_name, item_description, item_typeID, point_value, active)
VALUES 
	('Improper Behavior', 'Any improper action committed during the game', 1, -5, 1),
	('SmartPhone', 'Generic SmartPhone', 2, 2, 1),
	('DVD', 'Blue Compact Case with DVD', 2, 2, 1),
	('CD', 'Green Compact Case with regular CD', 2, 2, 1),
	('Mini CD', 'Smokey/Black Compact Case with mini CD', 2, 2, 1),
	('Floppy', '3 1/4 Black Floppy Diskette', 2, 2, 1),
	('Regular USB', 'Regular USB Drive on Lanyard', 2, 2, 1),
	('MP3 Player', 'Black MP3 Player with Headphones', 2, 2, 1),
	('Game Console', 'Play Station Portable (Black & Silver)', 2, 2, 1),
	('Camcorder', 'Blue JVC Video Camera', 2, 2, 1),
	('CF Card', 'Compact Flash Card', 2, 2, 1),
	('Sunglasses', 'Sunglasses with Headphones', 2, 2, 1),
	('SD Card', 'Regular SD memory Card', 2, 2, 1),
	('Laptop Hard Drive', '500GB 3.5\" inch hard drive disk', 2, 2, 1),
	('Micro SD Card', 'Very small SD memory Card', 2, 3, 1),
	('USB Wristband', 'Wristband with USB hidden inside', 2, 3, 1),
	('Memory Watch', 'Silver & Black watch with hidden USB in band', 2, 5, 1),
	('Pico USB', 'Miniture USB flash drive', 2, 5, 1),
	('USB Keychain', 'Keychain with hidden USB drive', 2, 10, 1),
	('Spy Coin', 'Hollow Coin with Micro SD Card inside', 2, 10, 1),
	('USB Pen', 'Ball Point pen with hidden USB drive in back', 3, 3, 1),
	('Password', 'Password to access evidence', 3, 5, 1),
	('Evidence Found', 'Evidence displayed on Laptop', 5, 15, 1),
	('Chose correct device on first try', NULL, 4, 15, 1);

-- --------------------------------------------------------

--
-- Data to be inserted into 'scenario'
--

INSERT INTO cyber_crime_scene.scenario 
	(scenario_name)
VALUES 
	("Business Security"),
	("Law Enforcement"),
	("Military"),
	("TSA");

-- --------------------------------------------------------

--
-- Data to be inserted into 'item_type'
--
	
INSERT INTO cyber_crime_scene.item_type 
	(item_type_name) 
VALUES
	('Improper Behavior'),
	('Digital Devices'),
	('Key Digital Devices'),
	('Attempt Bonus'),
	('Evidence');

-- --------------------------------------------------------