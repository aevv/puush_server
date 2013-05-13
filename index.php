<?php
//content of this file can be added to the front of another index, assuming you have rewriting enabled. example .htaccess:
/*
Options +FollowSymLinks +Indexes
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule .* /index.php [L]
*/
//however must be added before any html is sent to the client

if (!is_dir("puushsettings"))
{
	echo "no settings dir found";
	//you may not want to exit here if added to the front of another index.php
	exit();
}
include("puushsettings/settings.php");
$con = mysql_connect($pHost . ":" . $pPort, $pUser, $pPass);
if (!$con)
{
	die ("couldnt connect : " . mysql_error());
}
mysql_select_db($pDatabase, $con);
$query = explode("?", $_SERVER['REQUEST_URI']);
$queryUse = $query[0];
if (strlen($queryUse) == 7)
{
	$q = mysql_real_escape_string(substr($queryUse, 1, strlen($queryUse) - 1));
	$res = mysql_query("select * from " . $pPrefix . "upload inner join " . $pPrefix . "user on " . $pPrefix . "upload.user_id=" . $pPrefix . "user.user_id where " . $pPrefix . "upload.access_name = '".$q."'");
	while ($row = mysql_fetch_array($res))
	{		
		mysql_query("UPDATE " . $pPrefix . "upload SET views=". ($row['views']+1)." WHERE upload_id=".$row['upload_id']);
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
}
?>