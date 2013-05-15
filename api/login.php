<?php
session_start();
if (isset($_SESSION['k']))
{
	echo '{"status": "error", "message": "already logged in"}';
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
	die ("couldnt connect : " . mysql_error());
}
mysql_select_db($pDatabase, $con);

if (!isset($_POST['e']) || !isset($_POST['p']))
{
	echo '{"status": "error", "message": "bad parameters"}';
	exit();
}
$user = mysql_real_escape_string($_POST['e']);
$pw = mysql_real_escape_string($_POST['p']);
$pw = md5(md5($pw));
$ut = $pPrefix . "user";
$res = mysql_query("SELECT * FROM $ut where email = '$user' and passwd = '$pw'");
if (mysql_num_rows($res) == 1)
{
	while ($row = mysql_fetch_array($res))
	{
		$_SESSION['k'] = $row['api_key'];
		$_SESSION['e'] = $row['email'];
		echo '{"status": "success", "message": "log in"}';
	}
}
else
{
	echo '{"status": "error", "message": "invalid details"}';
}
?>