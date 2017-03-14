<?php
	include("classes/Login.php");
	$login = new Login();
	if ($login->isUserLoggedIn() == true)
	{
			
	}
	include("views/chat.php");
?>
