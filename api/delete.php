<?php
if (!is_dir("puushsettings"))
{
	echo "no settings dir found";
	exit();
}
include("puushsettings/settings.php");
$con = mysql_connect($pHost . ":" . $pPort, $pUser, $pPass);
if (!$con)
{
	die ("couldnt connect : " . mysql_error());
}
mysql_select_db($pDatabase, $con);

$access = mysql_real_escape_string($_GET['a']);
$key = $_SESSION['k'];
$user = $prefix."user";
$upload = $prefix."upload";
$res = mysql_query("SELECT * FROM $upload inner join $user on $upload.user_id = $user.user_id where $user.api_key = $key and $upload.access_name = $access");
if (mysql_num_rows($res) == 0)
{
	echo '{"error":"no file"}';
	exit();
}
while ($row = mysql_fetch_array($res))
{
	$folder = $row['folder'];
	$id = $row['upload_id'];
}
if (!file_exists($pUploadFolder . 'users/'.$folder.'/'.$id))
{
	echo '{"error":"file already deleted"}';
	exit();
}
else
{
	if (unlink($pUploadFolder . 'users/'.$folder.'/'.$id))
	{
		mysql_query("DELETE FROM " . $pPrefix . "upload WHERE access_name='".$q."'");
		echo '{"success":"file deleted"}';
	}
	else
	{
		echo '{"error":"file could not be deleted"}';
	}
}
?>