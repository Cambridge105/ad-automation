<?php
include("template.htm");

?>

<form method="POST" action="createLog.php">
<input type="hidden" name="auto" value="false">
Create log for <input type="date" name="ad_date" /><input type="submit" value="OK" />
</form>

<?php
include("template-bottom.htm");
?>