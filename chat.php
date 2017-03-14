<?php
// show potential errors / feedback (from login object)
if (isset($login)) {
    if ($login->errors) {
        foreach ($login->errors as $error) {
            echo $error;
        }
    }
    if ($login->messages) {
        foreach ($login->messages as $message) {
            echo $message;
        }
    }
}

include("classes/User.php");
include("classes/Post.php");
include("classes/Comment.php");
include("classes/Database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="js/jquery.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/chat.css">
</head>
<body>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.6.8-fix/jquery.nicescroll.min.js"></script>
<h4>Chat</h4>
<div class="content container-fluid bootstrap snippets">
    <div class="row">
        <div class="col-sm-9 col-xs-12 chat" style="outline: none; padding: 0;">
            <div class="col-inside-lg decor-default">
                <div class="chat-body">
					<?php
					if (isset($_GET['post_id']))
					{
						$db = new Database();	
						$comments = $db->getComments($_GET['post_id']);
						
						// Render each comment.
						foreach ($comments as $comment)
						{
							echo '
							<div class="answer left">
									<div class="avatar">
										<img src="images/avatar.png" alt="User name">
										<div class="status offline"></div>
									</div>
									<div class="name">' . $comment->getUser()->getUserName() . '</div>
									<div class="text"'; if($comment->getUser()->getUserId() == $db->getUserId($_SESSION['user_name'])) echo 'style="background: #7266ba; color: #fff;"'; echo '>' 
										. $comment->getContent() .
									'</div>
									<div class="time">5 min ago | <a href="report_post.php?post_id='.$_GET['post_id'].'&reported_user='.$comment->getUser()->getUserName().'">Report</a></div>'; 
							if ($db->isUserAdmin($_SESSION['user_name']) == 1)
							{
    							echo '<a href="remove_comment.php?comment_id='.$comment->getId().'">Remove</a>';
							}
							echo'	</div>
							';
						}
					}
					?>

                    <div class="answer-add">
     					<?php
     						if ($login->isUserLoggedIn() == true)
     						{
     							echo '
                                        <form method="post" action="create_comment.php" name="createCommentForm">
                                            <input name="comment_content" placeholder="Write a message" type="text">
                                            <input name="parent_post_id" type="hidden" value="' .$_GET['post_id'].'">
                                            <input class="answer-btn answer-btn-2" type="submit" name="createComment" style="text-indent:-9999px;"/>
                                        </form>
     							';
     						}	  
     					?>
                   </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(function(){
        //$(".chat").niceScroll();
    })
</script>
</body>
</html>
