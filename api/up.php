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
if (!isset($_POST['k']))
{
	echo "-1";
	exit();
}
$key = mysql_real_escape_string($_POST['k']);
$user = $pPrefix . "user";
$upload = $pPrefix . "upload";
$res = mysql_query("SELECT * FROM $user WHERE api_key='$key'");
$user_id = -1;
$folder = "";
$priv = 0;
if (mysql_num_rows($res) == 0)
{
	echo "-1"; 
	break;
}
while ($row = mysql_fetch_array($res))
{
	$user_id = $row['user_id'];
	$folder = $row['folder'];
	$priv = $row['default_privacy'];
}

$found = false;
while (!$found)
{
	$access = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 6)), 0, 6);
	$access_res = mysql_query("SELECT * FROM $upload WHERE access_name='$access'");
	$got = false;
	while ($access_row = mysql_fetch_array($access_res))
	{	
		$got = true;
	}
	if (!$got)
	{
		$found = true;
	}
}
if (!is_dir($pUploadFolder . "users/$folder/"))
{
	mkdir($pUploadFolder . "users/$folder/");
}
$file = mysql_real_escape_string($_FILES["f"]["name"]);
mysql_query("INSERT INTO $upload (name, time, user_id, access_name, private) VALUES ('$file', ".time().", $user_id, '$access', $priv)");
$id = mysql_insert_id();
move_uploaded_file($_FILES["f"]["tmp_name"], $pUploadFolder. "users/$folder/" . $id);
echo "0,http://$pDomain/$access,$id,0";
?>