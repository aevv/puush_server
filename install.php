<?php
//REMOVE THIS FILE AFTER USE

//variables - change these to match your setup
$host = "localhost";
$database = "puush";
$prefix = "puush_";
$user = "user";
$pass = "pass";
$port = "3306";
$uploadFolder = "/home/user/web/puush/"; //absolute folder name to store files, such as /home/name/puush/. do not put in a web available directory
$domain = "example.com";


$con = mysql_connect($host . ":" . $port, $user, $pass);
if (!$con)
{
	echo "couldn't connect to server: " . mysql_error();
	exit();
}
$result = mysql_query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $database . "'");
$exits = false;
if (mysql_num_rows($result) > 0)
{
	$exists = true;
	echo "database already exists, using it<br/>";
}
else if (mysql_query("CREATE DATABASE " . $database))
{
	$exists = true;
	echo "database created<br/>";
}
if ($exists)
{
	mysql_select_db($database, $con);
	if (mysql_query("CREATE TABLE " . $prefix . "user(user_id INT NOT NULL AUTO_INCREMENT, email TEXT NOT NULL, passwd TEXT NOT NULL, api_key TEXT NOT NULL, folder TEXT NOT NULL, default_privacy INT NOT NULL DEFAULT '0', private_folder INT NOT NULL DEFAULT '0', PRIMARY KEY(user_id))"))
	{
		echo "created users table " . $prefix . "user<br/>";
	}
	else
	{
		$error = mysql_error();
		$userCheck = mysql_query("SELECT * FROM information_schema.tables WHERE table_schema = '" . $database ."' AND table_name = '" . $prefix . "user' LIMIT 1;");
		if (mysql_num_rows($userCheck) == 0)
		{
			echo "could not create user table: " . $error;
			exit();
		}
		else
		{
			echo "users table already exists<br/>";
		}
	}
	if (mysql_query("CREATE TABLE " . $prefix . "upload(upload_id INT NOT NULL AUTO_INCREMENT, name TEXT NOT NULL, time INT NOT NULL, user_id INT NOT NULL, access_name TEXT NOT NULL, private INT NOT NULL, views INT NOT NULL, PRIMARY KEY(upload_id))"))
	{
		echo "created uploads table " . $prefix . "upload<br/>";
	}
	else
	{
		$error = mysql_error();
		$uploadCheck = mysql_query("SELECT * FROM information_schema.tables WHERE table_schema = '" . $database ."' AND table_name = '" . $prefix . "upload' LIMIT 1;");
		if (mysql_num_rows($uploadCheck) == 0)
		{
			echo "could not create upload table: " . $error;
			exit();
		}
		else
		{
			echo "uploads table already exists<br/>";
		}
	}
	if (!is_dir("puushsettings"))
	{
		mkdir("puushsettings");
		echo "settings folder created<br/>";
	}
	
	$file = fopen("puushsettings/settings.php", "w");
	fwrite($file, "<?php \$pHost = \"" . $host . "\";
	\$pDatabase = \"" . $database . "\";
	\$pPrefix = \"" . $prefix . "\";
	\$pUser = \"" . $user . "\";
	\$pPass = \"" . $pass . "\";
	\$pPort = \"" . $port . "\";
	\$pUploadFolder = \"" . $uploadFolder . "\"; 
	\$pDomain = \"" . $domain . "\";
	?>");
	fclose($file);
	
	$access = fopen("puushsettings/.htaccess", "w");
	fwrite($access, "order allow,deny
deny from all");
	fclose($access);
	
	$rewrite = fopen(".htaccess", "w");
	fwrite($rewrite, "
Options +FollowSymLinks +Indexes
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule .* /index.php [L]");
	fclose($rewrite);
	
	//this could do with improving, returns true if files existed but couldn't be written to?
	if (file_exists("puushsettings/settings.php") && file_exists("puushsettings/.htaccess") && file_exists(".htaccess"))
	{
		echo "all done, delete install.php<br/>you can edit puushsettings/settings.php to change any values at a later date";
		exit();
	}
	else
	{
		echo "something went wrong and settings files could not be made, probably with errors displayed above";
	}
}
else
{
	echo "could not create database table: " . mysql_error();
	exit();
}
?>