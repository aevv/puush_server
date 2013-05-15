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

//refactor this later to not have so much duplicating code
if (isset($_POST['i']))
{
	if (!isset($_POST['k']))
	{
		echo "-1";
		exit();
	}
	$id = mysql_real_escape_string($_POST['i']);
	$key = mysql_real_escape_string($_POST['k']);
	$user = $pPrefix."user";
	$upload = $pPrefix."upload";
	
	$res = mysql_query("SELECT * FROM $upload inner join $user on $upload.user_id = $user.user_id where $upload.upload_id = $id and $user.api_key = '$key'");
	if (mysql_num_rows($res) == 0)
	{
		echo "-1";
		exit();
	}
	
	while ($row = mysql_fetch_array($res))
	{
		$folder = $row['folder'];
	}
	if (!file_exists($pUploadFolder . 'users/'.$folder.'/'.$id))
	{
		echo "-1";
		mysql_query("DELETE FROM $upload WHERE upload_id=$id");
		exit();
	}
	else
	{
		if (unlink($pUploadFolder . 'users/'.$folder.'/'.$id))
		{
			mysql_query("DELETE FROM $upload WHERE upload_id=$id");
		}
		else
		{
			echo "-1";
			exit();
		}
	}
	$res = mysql_query("SELECT * FROM $upload inner join $user on $upload.user_id = $user.user_id where $user.api_key='$key' order by $upload.time desc limit 5");
	$ret = "";
	$first = true;
	while ($row = mysql_fetch_array($res))
	{
		if ($first)
		{
			$first = false;
			$ret = $ret . "0\n";
		}
		$ret = $ret . $row['upload_id'].",". date("Y-m-d H:i:s", $row['time']) .",http://$pDomain/".$row['access_name'].",".$row['name'].",".$row['views'].",0\n";
	}
	if ($ret == "")
	{
		$ret = "-2";
	}
	echo $ret;
	exit();
}

session_start();

if (!isset($_SESSION['k']))
{
	echo '{"status": "error", "message": "not logged in"}';
	exit();
}


if (!isset($_POST['a']))
{
	echo '{"status": "error", "message": "bad parameters"}';
	exit();
}

$access = mysql_real_escape_string($_POST['a']);
$key = $_SESSION['k'];
$user = $pPrefix."user";
$upload = $pPrefix."upload";
$res = mysql_query("SELECT * FROM $upload inner join $user on $upload.user_id = $user.user_id where $user.api_key = '$key' and $upload.access_name = '$access'");
if (mysql_num_rows($res) == 0)
{
	echo '{"status": "error", "message": "no file"}';
	exit();
}
while ($row = mysql_fetch_array($res))
{
	$folder = $row['folder'];
	$id = $row['upload_id'];
}
if (!file_exists($pUploadFolder . 'users/'.$folder.'/'.$id))
{
	echo '{"status": "error", "message": "file already deleted"}';
	mysql_query("DELETE FROM $upload WHERE access_name='$access'");
	exit();
}
else
{
	if (unlink($pUploadFolder . 'users/'.$folder.'/'.$id))
	{
		mysql_query("DELETE FROM $upload WHERE access_name='$access'");
		echo '{"status": "success", "message": "file deleted"}';
	}
	else
	{
		echo '{"status": "error", "message": "file could not be deleted"}';
	}
}
?>