<?php
//Find the next break
require_once("/var/www/adverts/db_credentials.php");											// Not committed to Git, this is a three-line PHP file containing the definitions of three string variables $db_host, $db_user and $db_pass 
$mysql_ad_conn = new mysqli($db_host, $db_user, $db_pass, 'adverts');
$mysql_rd_conn = new mysqli($db_host, $db_user, $db_pass, 'Rivendell');
$sql = "SELECT MIN(break_time) AS next_break FROM logs WHERE break_time>NOW() LIMIT 1";
$result = $mysql_ad_conn->query($sql);
if (!$result) {
	echo "No break set!";
	// TODO: SEND ALERT EMAIL - NO BREAK SET
	exit;
}
$row = $result->fetch_array();
$next_break = $row["next_break"];
		
// Write out break as log file
$sql = "SELECT ad_time,cart FROM logs WHERE break_time='" . $next_break . "'";
$result = $mysql_ad_conn->query($sql);
$pk=0;
$mysql_rd_conn->query("TRUNCATE TABLE `ADS_LOG`;");
$insert = "INSERT INTO `ADS_LOG` (`ID`, `COUNT`, `TYPE`, `SOURCE`, `START_TIME`, `GRACE_TIME`, `CART_NUMBER`, `TIME_TYPE`, `POST_POINT`, `TRANS_TYPE`, `START_POINT`, `END_POINT`, `FADEUP_POINT`, `FADEUP_GAIN`, `FADEDOWN_POINT`, `FADEDOWN_GAIN`, `SEGUE_START_POINT`, `SEGUE_END_POINT`, `SEGUE_GAIN`, `DUCK_UP_GAIN`, `DUCK_DOWN_GAIN`, `COMMENT`, `LABEL`, `ORIGIN_USER`, `ORIGIN_DATETIME`, `LINK_EVENT_NAME`, `LINK_START_TIME`, `LINK_LENGTH`, `LINK_START_SLOP`, `LINK_END_SLOP`, `LINK_ID`, `LINK_EMBEDDED`, `EXT_START_TIME`, `EXT_LENGTH`, `EXT_CART_NAME`, `EXT_DATA`, `EXT_EVENT_ID`, `EXT_ANNC_TYPE`) VALUES";
while ($row = $result->fetch_array()) {
	$insert .= "({$pk}, " . ($pk+1) . ", 0, 0, 0, 0, " . $row["cart"] . ", 0, 'N', 0, -1, -1, -1, -3000, -1, -3000, -1, -1, -3000, 0, 0, '', '', '', '0000-00-00 00:00:00', '', 0, 0, 0, 0, -1, 'N', '00:00:00', -1, '', '', '', ''),";
	$pk++;
}
$insert = substr($insert, 0, strlen($insert)-1) . ";"; // Strip the final comma and apply semi-colon
$result = $mysql_rd_conn->query($insert);
if (!$result) {echo $mysql_rd_conn->error;}


?>