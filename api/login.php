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

$user = mysql_real_escape_string($_POST['e']);
$pw = mysql_real_escape_string($_POST['p']);
$ut = $pPrefix . "user";
$res = mysql_query("SELECT * FROM $ut where email = '$user' and passwd = '$pw'");
if (mysql_num_rows($res) == 1)
{
	while ($row = mysql_fetch_array($res))
	{
		session_start();
		$_SESSION['k'] = $row['api_key'];
		$_SESSION['e'] = $row['email'];
		echo '{"success":"log in"}';
	}
}
else
{
	echo '{"error":"invalid details"}';
}
?>