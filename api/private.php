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


$key = mysql_real_escape_string($_SESSION['k']);

if (isset($_GET['a']))
{
	$access = mysql_real_escape_string($_GET['a']);
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
		$private = $row['private'];
		if ($private == 0)
		{
			mysql_query("UPDATE $upload SET private = 1 WHERE access_name = '$access'");
			echo '{"success":"private"}';
		}
		else
		{
			mysql_query("UPDATE $upload SET private = 0 WHERE access_name = '$access'");
			echo '{"success":"public"}';
		}
	}
}
?>