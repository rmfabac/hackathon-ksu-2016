<?php
include("classes/Login.php");
$login = new Login();

if ($login->isUserLoggedIn() == true)
{
    if (!empty($_POST['post_title']) && !empty($_POST['post_content']))
    {
     	include("classes/User.php");
    	include("classes/Post.php");
    	include("classes/Database.php");
    	$db = new Database();
    	$db->makeNewPost($_SESSION['user_name'], $_POST['post_title'], $_POST['post_content'], "Kansas");	
		header("Location: http://159.203.142.195");
    }
    
    // show the register view (with the registration form, and messages/errors)
    include("views/create_post.php");
}
else 
{
	header("Location: http://159.203.142.195");
}
?>
