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
	die ("couldnt connect : " . mysql_error());
}
mysql_select_db($pDatabase, $con);
$canMake = false;
$email = mysql_real_escape_string($_POST["e"]);
if (isset($_POST["p"]))
{
	$passwd = mysql_real_escape_string($_POST["p"]);
	if ($passwd == "")
	{
		exit();
	}

	$passwd = md5(md5($passwd));

	$res = mysql_query("SELECT * FROM " . $pPrefix . "user WHERE email='".$email."' AND passwd='".$passwd."'");
	
	$canMake = true;
}
else if (isset($_POST["k"]))
{
	$key = mysql_real_escape_string($_POST["k"]);
	
	$res = mysql_query("SELECT * FROM " . $pPrefix . "user WHERE email='".$email."' AND api_key='".$key."'");
}
$exists = false;
while ($row = mysql_fetch_array($res))
{
	$exists = true;
	echo "1,".$row['api_key'].","."2020-09-09,0";
	exit();
}
if (!$exists && $canMake && ($email != "") && ($passwd != ""))
{	
	$res = mysql_query("SELECT * FROM " . $pPrefix . "user WHERE email='".$email."'");
	while ($row = mysql_fetch_array($res))
	{
		echo "-1";
		exit();
	}
	$found = false;	
	while (!$found)
	{		
		$api_key = md5(uniqid(mt_rand(), true));
		$res = mysql_query("SELECT * FROM " . $pPrefix . "user WHERE api_key='".$api_key."'");
		$got = false;
		while ($row = mysql_fetch_array($res))
		{	
			$got = true;
		}
		if (!$got)
		{
			$found = true;
		}
	}
	
	$found2 = false;
	while (!$found2)
	{
		$folder = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 5);
		$res2 = mysql_query("SELECT * FROM " . $pPrefix . "user WHERE folder='".$folder."'");
		$got2 = false;
		while ($row2 = mysql_fetch_array($res2))
		{	
			$got2 = true;
		}
		if (!$got2)
		{
			$found2 = true;
		}
	}
	mysql_query("INSERT INTO " . $pPrefix . "user(email, passwd, api_key, folder) VALUES('".$email."', '".$passwd."', '".$api_key."', '".$folder."')");
	echo "1,".$api_key.","."2020-09-09,0";
}
else
{
	echo "-1";
}
?>