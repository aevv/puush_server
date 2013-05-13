<?php
session_start();
if (!isset($_SESSION['k']))
{
	echo '{"error":"not logged in"}';
	exit();
}
if (!is_dir("../puushsettings"))
{
	echo "no settings dir found";
	exit();
}
include("../puushsettings/settings.php");
$con = mysql_connect($pHost . ":" . $pPort, $pUser, $pPass);
if (!$con)
{
	echo "mysql connection failed: " . mysql_error();
	exit();
}
mysql_select_db($pDatabase, $con);

if (!isset($_GET['a']))
{
	echo '{"error":"bad parameters"}';
	exit();
}

$access = mysql_real_escape_string($_GET['a']);
$key = $_SESSION['k'];
$user = $pPrefix."user";
$upload = $pPrefix."upload";
$res = mysql_query("SELECT * FROM $upload inner join $user on $upload.user_id = $user.user_id where $user.api_key = '$key' and $upload.access_name = '$access'");
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
	mysql_query("DELETE FROM $upload WHERE access_name='$access'");
	exit();
}
else
{
	if (unlink($pUploadFolder . 'users/'.$folder.'/'.$id))
	{
		mysql_query("DELETE FROM $upload WHERE access_name='$access'");
		echo '{"success":"file deleted"}';
	}
	else
	{
		echo '{"error":"file could not be deleted"}';
	}
}
?>