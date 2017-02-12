<?php
include("template.htm");
$uploadOk = 0;

if ($_FILES)
{
	$target_dir = "/var/www/uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$isMP3=false;
	$isWAV=false;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);


	// Check if file already exists
	if (file_exists($target_file)) {
		 echo "Sorry, file already exists.";
		 $uploadOk = 0;
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 10000000) {
		 echo "Sorry, your file is too large.";
		 $uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType == "mp3") {$isMP3 = true;} elseif ($imageFileType=="wav") {$isWAV=true;} else{
		 echo "Sorry, only MP3 and WAV files are allowed.";
		 $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		 echo "Sorry, your file was not uploaded."; // if everything is ok, try to upload file
		 } else {
		 if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],
	$target_file)) {
			 echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		 } else {
			 echo "Sorry, there was an error uploading your file.";
		 }
	}
}


if ($uploadOk == 1)
{
	sleep(1);

	require_once("db_credentials.php");											
	$mysql_ad_conn = new mysqli($db_host, $db_user, $db_pass, 'adverts');


	if ($isMP3) {
		exec("rdimport --verbose --delete-source --segue-level=-16 ADS " . $target_dir . "*.mp3",$output);
		}
	if ($isWAV) {
		exec("rdimport --verbose --delete-source --segue-level=-16 ADS " . $target_dir . "*.wav",$output);
		}
	// Looking for something like 
	// Importing file "060108_001.wav" to cart 060601 ... done. 
	// in output
	
	$cart = 0;
	foreach ($output as $outputLine)
	{
		if (strpos($outputLine,"to cart"))
		{
			echo "<p>Rivendell: " . $outputLine . "</p><hr>";
			preg_match("/to cart (\d+)/",$outputLine,$matches);
			$cart = $matches[1];
		}
	}
	if ($cart > 0)
	{
		$date_to = DateTime::createFromFormat('!d/m/Y', mysqli_real_escape_string($mysql_ad_conn, ($_POST['date_to'])));
		$date_from = DateTime::createFromFormat('!d/m/Y', mysqli_real_escape_string($mysql_ad_conn, ($_POST['date_from'])));
		
		//INSERT into metadata table
		if (!($stmt = $mysql_ad_conn->prepare("INSERT INTO metadata(advertiser, cart, date_from, date_to, plays_per_day) VALUES (?, ?, ?, ?, ?)"))) {
				echo "Prepare failed: (" . $mysql_ad_conn->errno . ") " . $mysql_ad_conn->error;
			}
			if (!$stmt->bind_param('sissi', mysqli_real_escape_string($mysql_ad_conn, $_POST['advertiser']), $cart, date("Y-m-d",$date_from->getTimestamp()), date("Y-m-d",$date_to->getTimestamp()), mysqli_real_escape_string($mysql_ad_conn, $_POST['plays_per_day']))) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$stmt->execute(); 
	}

	
}

?>

<form action="index.php" method="post" enctype="multipart/form-data"> <div>
     <label for="fileToUpload">Select audio to upload:</label>
     <input type="file" name="fileToUpload" id="fileToUpload"> </div><div>
     <label for="advertiser">Advertiser name:</label>
     <input type="text" name="advertiser" id="advertiser" size="30"> </div><div>
     <label for="date_from">Play advert from:</label>
     <input type="date" name="date_from" idd="date_from"> </div><div>
     <label for="date_to">Until:</label>
     <input type="date" name="date_to" id="date_to"> </div><div>
     <label for="plays_per_day">Minimum plays per day:</label>
     <input type="number" size="3" name="plays_per_day" 
id="plays_per_day">
</div><div>
     <input type="submit" value="Upload Advert" name="submit"> </div> </form>

<?php
include("template-bottom.htm");
?>
