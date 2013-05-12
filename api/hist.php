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
if (isset($_POST['k']))
{
	$key = mysql_real_escape_string($_POST['k']);
}
else
{
	$key = "";
}
$res = mysql_query("SELECT * FROM " . $pPrefix . "upload inner join " . $pPrefix . "user on " . $pPrefix . "upload.user_id = " . $pPrefix . "user.user_id where " . $pPrefix . "user.api_key='".$key."' order by " . $pPrefix . "upload.time desc limit 5");
$ret = "";
$first = true;
while ($row = mysql_fetch_array($res))
{
	if ($first)
	{
		$first = false;
		$ret = $ret . "0\n";
	}
	$ret = $ret . $row['upload_id'].",". date("Y-m-d H:i:s", $row['time']) .",http://" . $pDomain . "/".$row['access_name'].",".$row['name'].",".$row['views'].",0\n";
}
if ($ret == "")
{
	$ret = "-2";
}
echo $ret;
?>