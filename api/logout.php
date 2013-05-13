<?php
session_start();
if (isset($_SESSION['k']))
{
	unset($_SESSION['k']);
	echo '{"success":"logged out"}';
}
else
{
	echo '{"error":"not logged in"}';
}
?>