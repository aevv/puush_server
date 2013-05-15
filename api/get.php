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
	die ("couldnt connect : " . mysql_error());
}
mysql_select_db($pDatabase, $con);

if (!isset($_POST['a']))
{
	exit();
}
$access = mysql_real_escape_string($_POST['a']);
$user = $pPrefix."user";
$upload = $pPrefix."upload";

$res = mysql_query("select * from $upload inner join $user on $upload.user_id = $user.user_id where $upload.access_name = '$access'");
while ($row = mysql_fetch_array($res))
{		
	$folder = $row['folder'];		
	$name = $row['name'];
	$id = $row['upload_id'];
	$file_extension = strtolower(substr(strrchr($name,"."),1));
	$force = true;
	switch( $file_extension ) {
		case "gif": $ctype="image/gif"; $force = false; break;
		case "png": $ctype="image/png"; $force = false; break;
		case "jpeg":
		case "jpg": $ctype="image/jpg"; $force = false; break;
		case "swf": $ctype="application/x-shockwave-flash"; $force = false; break;
		case "php": $force = true; break;
		case "html": $force = true; break;
		case "js": $force = true; break;
		default:
			$force = false; $ctype="application/octet-stream"; 
			header('Content-Disposition: attachment; filename="'.$name.'"'); 
			break;
	}		
	if (!$force)
	{
		header('Content-type: '.$ctype);			
	}
	else		
	{
		header('Content-type: text/plain');		
	}
	readfile($pUploadFolder . 'users/'.$folder.'/'.$id);
	exit();
}
?>