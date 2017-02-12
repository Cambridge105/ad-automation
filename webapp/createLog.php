<?php

require_once("db_credentials.php");											// Not committed to Git, this is a three-line PHP file containing the definitions of three string variables $db_host, $db_user and $db_pass 
require_once("classes.php");
include("template.htm");

//Open connection to databases
$mysql_rd_conn = new mysqli($db_host, $db_user, $db_pass, 'Rivendell');
$mysql_ad_conn = new mysqli($db_host, $db_user, $db_pass, 'adverts');
$auto_and_day_has_ads = false;

if (!$_POST['ad_date'] && $_POST['auto'] == "false")
{
	echo "<p>No date supplied!</p>";
	exit;
}

if ($_POST['auto'] == "false")
{
	// Auto is false if the submit came from schedule.php
	$input_date = mysqli_real_escape_string($mysql_rd_conn, $_POST['ad_date']);
	$tmp_date = DateTime::createFromFormat('!d/m/Y', mysqli_real_escape_string($mysql_ad_conn, ($_POST['ad_date'])));
	$input_date = date("Y-m-d",$tmp_date->getTimestamp());
	// Delete any existing entries for the day
	$sql = "DELETE FROM logs WHERE ad_time LIKE '" . $input_date . "%'";
	$mysql_ad_conn->query($sql);
}
else 
{
	// Auto is true if called from Cronjob. In that case default to the following day
	$input_date = date("Y-m-d", (time()+86400));
	$sql = "SELECT * FROM logs WHERE ad_time LIKE '" . $input_date . "%' LIMIT 1";
	 if ($result = $mysql_ad_conn->query($sql)) {
		$auto_and_day_has_ads = true;	//Will prevent the existing ads from being overwritten later
	}
}

$idents_cache = getAllCartsInGroup("IDENTS","FORCED_LENGTH < 16000");
$promos_cache = getAllCartsInGroup("PROMOS");
$ads_cache = getAllAdverts($input_date);


$ad_template = array("IDENTS","ADS","ADS","PROMOS","IDENTS");			// These are the Rivendell group types that form the break
$ad_fallback = "PROMOS";							// If there is no Ad to play in an ADS slot defined in the template above, what Rivendell group should be played instead?


// When advert breaks happen.
$ads_per_day = array();
$ads_per_day[0] = array("08:20","08:40","09:20","09:40","10:20","10:40");
$ads_per_day[1] = array("06:20","06:40","07:20","07:40","08:20","08:40","09:20","09:40","10:20","10:40","11:20","11:40","12:20","12:40","13:20","13:40","14:20","14:40","15:20","15:40","16:20","16:40","17:20","17:40","18:20","18:40");
$ads_per_day[2] = array("06:20","06:40","07:20","07:40","08:20","08:40","09:20","09:40","10:20","10:40","11:20","11:40","12:20","12:40","13:20","13:40","14:20","14:40","15:20","15:40","16:20","16:40","17:20","17:40","18:20","18:40");
$ads_per_day[3] = array("06:20","06:40","07:20","07:40","08:20","08:40","09:20","09:40","10:20","10:40","11:20","11:40","12:20","12:40","13:20","13:40","14:20","14:40","15:20","15:40","16:20","16:40","17:20","17:40","18:20","18:40");
$ads_per_day[4] = array("06:20","06:40","07:20","07:40","08:20","08:40","09:20","09:40","10:20","10:40","11:20","11:40","12:20","12:40","13:20","13:40","14:20","14:40","15:20","15:40","16:20","16:40","17:20","17:40","18:20","18:40");
$ads_per_day[5] = array("06:20","06:40","07:20","07:40","08:20","08:40","09:20","09:40","10:20","10:40","11:20","11:40","12:20","12:40","13:20","13:40","14:20","14:40","15:20","15:40","16:20","16:40","17:20","17:40","18:20","18:40");
$ads_per_day[6] = array("08:20","08:40","09:20","09:40","10:20","10:40");

if (!$auto_and_day_has_ads)
{
	$all_breaks = makeAdvertBreaks($input_date);
	foreach ($all_breaks as $log)
	{
	  $log->showTable();
	  $log->writeToDb();
	}
}
else
{
	echo "<p>Adverts already created for this date.</p>";
}
include("template-bottom.htm");


// ===============================================
//               FUNCTIONS 
// ===============================================


function getAllCartsInGroup($group_name,$where_clause = "1")
{
	// Returns an array of all the carts in the group
	// If $where_clause specified, this is appended to the MySQL query
	 global $mysql_rd_conn;
	 $return = array();
	 
	 $sql = "SELECT NUMBER,TITLE,FORCED_LENGTH FROM CART WHERE GROUP_NAME='" . $group_name . "' AND " . $where_clause;
	 if (!$result = $mysql_rd_conn->query($sql)) {
		return false;
		}
	
	while ($row = $result->fetch_array()) {
		$tmp = new Cart;
		$tmp->number = $row["NUMBER"];
		$tmp->title = $row["TITLE"];
		$tmp->length = $row["FORCED_LENGTH"];
		array_push($return,$tmp);
    	}	

	return $return;
}	


function getAllAdverts($date)
{
  global $mysql_ad_conn, $mysql_rd_conn;
	$return = array();
	$tmp_date = strtotime($date . " 12:00:00");
	$sql = "SELECT cart, plays_per_day FROM metadata WHERE UNIX_TIMESTAMP(date_from)<=" . $tmp_date . " AND UNIX_TIMESTAMP(date_to)>" . $tmp_date;
	if (!$result = $mysql_ad_conn->query($sql)) {
    return false;
		}
	while ($row = $result->fetch_array()) {
		$tmp = new Cart;
		$tmp->number = $row["cart"];
		$tmp->playsPerDayNeeded = $row["plays_per_day"];
		$tmp->playsPerDayRemaining = $row["plays_per_day"];
		$sql2 = "SELECT TITLE,FORCED_LENGTH FROM CART WHERE NUMBER=" . $row["cart"];
		$result2 = $mysql_rd_conn->query($sql2);
		$row2 = $result2->fetch_array();
		$tmp->title = $row2["TITLE"];
		$tmp->length = $row2["FORCED_LENGTH"];
		$tmp->lastPlayed = 0;
		array_push($return,$tmp);
    	}	
      return $return;
}

function makeAdvertBreaks($input_date)
{
      global $ads_per_day, $ad_template, $ad_fallback;
      $tmp_date = strtotime($input_date);
      $day = date("w", $tmp_date);                     // Day of the week from 0=Sun to 6=Sat
      echo "<h1>Advert breaks for " . date("l j F",$tmp_date) . " </h1>";
      $all_breaks_in_day = array();
      $break_num = 0;
      $slot_num = 0;
      foreach ($ads_per_day[$day] as $this_break_time)
      {
            // --------------- This level represents a whole break -------------------------
            $log = new Log;
            $log->datetime = strtotime($input_date . " " . $this_break_time . ":00");
            $log->contentArray = array();
            $lastCartTime = $log->datetime;
	          
             foreach ($ad_template as $slot)
             {
                  // ------------ This level represents an individual ad within the break --------
	               if ($slot != "ADS")
	               {
		                    $cart = getRandomCartOfGroup($slot);
	               }
	               else 
	               {
		                    $cart = getAdvert($lastCartTime);
		                    if (!$cart) // There's no advert to play
		                    {
			                      $cart = getRandomCartOfGroup($ad_fallback);
		                    }
	               }
                 $log->contentArray[$slot_num] = $cart;
                 $slot_num++;
            }
            
            $all_breaks_in_day[$break_num] = $log;
            $break_num++;
            $slot_num = 0;
       }
       return $all_breaks_in_day;     
}
  
         

function getRandomCartOfGroup($groupname)
{
	/* When passed a Rivendell group name, this function selects one Cart 
	   matching that group at random without consideration of when it was
	   last played.
	   Note: This means it's possible the same cart could be played more than
	   once in the same break. I probably want to avoid this. 
	 */
	 global $mysql_rd_conn, $idents_cache, $promos_cache;

	 switch ($groupname)
	 {
		case "IDENTS": $use = $idents_cache; break;
		case "PROMOS": $use = $promos_cache; break;
		default: return false;
	 }
	 	$return = $use[(rand(0,sizeof($use)-1))];
    return $return;
}


function getAdvert($advertTime) 
{
	global $ads_cache;
	$ninetyMinsAgo = $advertTime - 5400;
	// Iterate over the ads looking for those that haven't been played in the last 90 mins and which still need to be played today
	$candidate_ads = array();
	foreach ($ads_cache as $ad)
	{
		if ($ad->playsPerDayRemaining > 0 && $ad->lastPlayed < $ninetyMinsAgo)
		{
			array_push($candidate_ads,$ad);
		}
	}
	if (sizeof($candidate_ads) < 1) {return false;}
	$return = $candidate_ads[(rand(0,sizeof($candidate_ads)-1))];
	// Update the cache with the new last played time
	for ($i=0; $i<sizeof($ads_cache); $i++)
	{
		if ($ads_cache[$i]->number == $return->number)
		{
			$ads_cache[$i]->lastPlayed = $advertTime;
			$ads_cache[$i]->playsPerDayRemaining = $ads_cache[$i]->playsPerDayRemaining - 1;
		}
	}
	return $return;
}


?>