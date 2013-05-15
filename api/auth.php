<?php
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
$canMake = false;

if (!isset($_POST["e"]))
{
	echo "-1";
	exit();
}
$user = $pPrefix."user";
$upload = $pPrefix."uplaod";
$email = mysql_real_escape_string($_POST["e"]);
if (isset($_POST["p"]))
{
	$passwd = mysql_real_escape_string($_POST["p"]);
	if ($passwd == "")
	{
		exit();
	}

	$passwd = md5(md5($passwd));

	$res = mysql_query("SELECT * FROM $user WHERE email='$email' AND passwd='$passwd'");
	
	$canMake = true;
}
else if (isset($_POST["k"]))
{
	$key = mysql_real_escape_string($_POST["k"]);
	
	$res = mysql_query("SELECT * FROM $user WHERE email='$email' AND api_key='$key'");
}
else
{
	echo "-1";
	exit();
}
$exists = false;
while ($row = mysql_fetch_array($res))
{
	$exists = true;	
	$io = popen('/usr/bin/du -sb '.$pUploadFolder. "users/" . $row['folder'] ."/", 'r');
    $size = intval(fgets($io,80));
    pclose($io);
	echo "1,".$row['api_key'].","."2020-09-09,$size";
	exit();
}
if (!$exists && $canMake && ($email != "") && ($passwd != ""))
{	
	$res = mysql_query("SELECT * FROM $user WHERE email='$email'");
	while ($row = mysql_fetch_array($res))
	{
		echo "-1";
		exit();
	}
	$findKey = false;	
	while (!$findKey)
	{		
		$api_key = md5(uniqid(mt_rand(), true));
		$res = mysql_query("SELECT * FROM $user WHERE api_key='$api_key'");
		$got = false;
		while ($row = mysql_fetch_array($res))
		{	
			$got = true;
		}
		if (!$got)
		{
			$findKey = true;
		}
	}
	
	$findFolder = false;
	while (!$findFolder)
	{
		$folder = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 5);
		$res2 = mysql_query("SELECT * FROM $user WHERE folder='$folder'");
		$got = false;
		while ($row2 = mysql_fetch_array($res2))
		{	
			$got = true;
		}
		if (!$got)
		{
			$findFolder = true;
		}
	}
	mysql_query("INSERT INTO $user(email, passwd, api_key, folder) VALUES('$email', '$passwd', '$api_key', '$folder')");
	echo "1,$api_key,"."2020-09-09,0";
}
else
{
	echo "-1";
}
?>