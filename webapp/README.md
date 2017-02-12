#About
This software allows you you to import advertisements into [Rivendell](https://github.com/ElvishArtisan/rivendell), schedule when they need to be broadcast, and then advert breaks are created according to a template. This can be either entirely automated via cron or checked in a web UI. The scheduled breaks are imported as a Rivendell Log just prior to broadcast time.

#Requirements
* Webserver (eg. Apache)
* MySQL database (should be installed with Rivendell)
* PHP 5.3+ with mysqli
* PHP5-CLI

#Installation
1. In RDLogEdit, create a new log called 'ADS'. It can be left empty.
1. Create a new database in MySQL called 'adverts'. This must be accessible (read/write) to the same user as the user that accesses the Rivendell database.
1. Run mysql/create_db.sql to create the new tables within the 'adverts' 
database.
1. Create a file inside webapp called 'db_credentials.php', which sets three variables: $db_user, $db_pass, $db_host (this should be
self-explanatory)
1. Set your webserver to serve /webapp If it's installed on a different machine from the database, ensure the webapp can connect to the database.
1. Ensure php.ini's upload_max_filesize is bigger than the largest advert file you'll want to upload (20MB should be OK), then restart PHP if necessary.
1. Ensure the webserver has read/write access to the uploads directory (this is where adverts will be temporarily saved between being uplaoded to the webapp, and being ingested into Rivendell) 
1. In 'index.php', ensure the paths to your uploads folder is correctly set as the value of $target_dir.
1. Set up a cron job to call the scheduler each evening, to create the advert logs for the following day (the value 'auto' set to true sets a default to the following day, and also prevents overwriting any logs for that day that have already been set up manually , so it's safe to use):
     curl --data "auto=true" http://localhost/adverts/schedule.php
1. Set up a cron job to call the log loader a few minutes before each scheduled break
     load_logs.php
1. In RDLibrary. create a new Macro Cart with the following lines:
    PM 2!
	LL 1 ADS 0!
1. On the Sound Panel in RDAirPlay, add a new button referencing the macro cart you just created.
