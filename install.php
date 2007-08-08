<?php

global $db;
global $amp_conf;

$autoincrement = (($amp_conf["AMPDBENGINE"] == "sqlite") || ($amp_conf["AMPDBENGINE"] == "sqlite3")) ? "AUTOINCREMENT":"AUTO_INCREMENT";
$sql = "CREATE TABLE IF NOT EXISTS announcement (
	announcement_id integer NOT NULL PRIMARY KEY $autoincrement,
	description VARCHAR( 50 ),
	recording VARCHAR( 255 ),
	allow_skip INT,
	post_dest VARCHAR( 255 ),
	return_ivr TINYINT(1) NOT NULL DEFAULT 0,
	noanswer TINYINT(1) NOT NULL DEFAULT 0,
	repeat_msg VARCHAR(2) NOT NULL DEFAULT ''
)";
$check = $db->query($sql);
if(DB::IsError($check)) {
	die_freepbx("Can not create annoucment table");
}

// Version 0.3 adds auto-return to IVR
$sql = "SELECT return_ivr FROM announcement";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
	// add new field
    $sql = "ALTER TABLE announcement ADD return_ivr TINYINT(1) NOT NULL DEFAULT 0;";
    $result = $db->query($sql);
    if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

// Version 0.4 adds auto-return to IVR
$sql = "SELECT noanswer FROM announcement";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
	// add new field
    $sql = "ALTER TABLE announcement ADD noanswer TINYINT(1) NOT NULL DEFAULT 0;";
    $result = $db->query($sql);
    if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

// Version 0.8 upgrade
$repeat = (($amp_conf["AMPDBENGINE"] == "sqlite") || ($amp_conf["AMPDBENGINE"] == "sqlite3")) ? "repeat":"`repeat`";
$sql = "SELECT $repeat FROM announcement";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(! DB::IsError($check)) {
    // Change field name because php5 was not happy with repeat
    //
    $sql = "ALTER TABLE announcement CHANGE $repeat repeat_msg VARCHAR( 2 ) NOT NULL DEFAULT '' ;"; 
    $result = $db->query($sql);
    if(DB::IsError($result)) {
            die_freepbx($result->getDebugInfo());
    }
}

// Version 0.6 adds repeat_msg
$sql = "SELECT repeat_msg FROM announcement";
$check = $db->getRow($sql, DB_FETCHMODE_ASSOC);
if(DB::IsError($check)) {
	// add new field
    $sql = "ALTER TABLE announcement ADD repeat_msg VARCHAR(2) NOT NULL DEFAULT '';";
    $result = $db->query($sql);
    if(DB::IsError($result)) { die_freepbx($result->getDebugInfo()); }
}

$results = array();
$sql = "SELECT announcement_id, post_dest FROM announcement";
$results = $db->getAll($sql, DB_FETCHMODE_ASSOC);
if (!DB::IsError($results)) { // error - table must not be there
	foreach ($results as $result) {
		$old_dest  = $result['post_dest'];
		$announcement_id    = $result['announcement_id'];

		$new_dest = merge_ext_followme(trim($old_dest));
		if ($new_dest != $old_dest) {
			$sql = "UPDATE announcement SET post_dest = '$new_dest' WHERE announcement_id = $announcement_id  AND post_dest = '$old_dest'";
			$results = $db->query($sql);
			if(DB::IsError($results)) {
				die_freepbx($results->getMessage());
			}
		}
	}
}

?>
