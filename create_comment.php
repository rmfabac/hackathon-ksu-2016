<?php
include("classes/Login.php");
$login = new Login();

if ($login->isUserLoggedIn() == true)
{
    if (!empty($_POST['comment_content']))
    {
     	include("classes/User.php");
    	include("classes/Post.php");
    	include("classes/Database.php");
    	$db = new Database();
    	$db->makeNewComment($db->getUserId($_SESSION["user_name"]), $_POST['parent_post_id'], $_POST['comment_content']);	
		header("Location: https://159.203.142.195/view_post.php?post_id=" . $_POST['parent_post_id']);
    }
}
else 
{
	header("Location: https://159.203.142.195");
}
?>
