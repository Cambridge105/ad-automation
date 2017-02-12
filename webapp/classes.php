<?php

class Cart {
	public $title;
	public $number;
	public $length;
	public $starttime; // NB UNIX TIMESTAMP
	public $lastPlayed;
	public $playsPerDayNeeded;
	public $playsPerDayRemaining;
};

class Log {
	public $datetime;
	public $contentArray;
	public $isSaved = false;

	function showTable() 
	{
		$lastCartTime  = $this->datetime;
		echo "<table class=\"adTable\"><thead><tr><th>" . date("H:i:s",$this->datetime) . "</th></tr></thead><tbody>";
		foreach ($this->contentArray as $cart)
		{
			$cart->starttime = $lastCartTime;
          	$lastCartTime = ceil($lastCartTime + ($cart->length / 1000));
			echo "<tr><td>" . date("H:i:s",$cart->starttime) . "</td><td>" . $cart->title . "</td></tr>";
		}
		echo "</tbody></table>";
	}

	function writeToDb()
	{
		global $mysql_ad_conn;
		foreach ($this->contentArray as $cart)
		{
			if (!($stmt = $mysql_ad_conn->prepare("INSERT INTO logs(break_time, ad_time, cart) VALUES (?, ?, ?)"))) {
				echo "Prepare failed: (" . $mysql_ad_conn->errno . ") " . $mysql_ad_conn->error;
			}
			if (!$stmt->bind_param('ssi', date("Y-m-d H:i:s",$this->datetime),  date("Y-m-d H:i:s",$cart->starttime), $cart->number)) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$stmt->execute(); 
		}
		$this->isSaved = true;
	}
}