<?php
session_start();
if (isset($_SESSION['k']))
{
	unset($_SESSION['k']);
	echo '{"status": "success", "message": "logged out"}';
}
else
{
	echo '{"status": "error", "message": "not logged in"}';
}
?>