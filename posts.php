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

include("classes/Database.php");
include("classes/User.php");
include("classes/Post.php");

$db = new Database();

// we need to pass in the user's location here
$posts = $db->getHomepagePosts("Kansas");
?>

<!-- login form box -->
<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8">
    <title>Posts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="../js/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/chat.css">
	<link rel="stylesheet" href="../css/main.css">
</head>
<body>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.6.8-fix/jquery.nicescroll.min.js"></script>
<h4>Posts</h4>
<div class="content container-fluid bootstrap snippets">
    <div class="row">
            <div class="col-inside-lg decor-default chat" style="overflow: hidden; outline: none;">
                <div class="chat-users">
				<?php
					// render each post
					foreach ($posts as &$post)
					{
					  //echo $post->getUser()->getUserName();
					  //echo $post->getTitle();
					  //echo $post->getMessage();
					  //echo $post->getDatePosted();
					  
					  echo '<div class="user">
								<a href="https://159.203.142.195/view_post.php?post_id=' . $post->getId() . '">
									<div class="avatar">
										<img src="images/avatar.png" alt="User name">
									<div class="status online"></div>
									</div>
									<div class="name">' . $post->getUser()->getUserName() . '</div>
									<div class="content-short">' . $post->getTitle() . '</div>
									</a><div>' . $post->getDateDiff() . ' | <a href="report_post.php?post_id='.$post->getId().'&reported_user='.$post->getUser()->getUserName().'">Report</a>';
								if ($db->isUserAdmin($_SESSION['user_name']) == 1)
								{
									echo ' | <a href="remove_post.php?post_id='.$post->getId().'">Remove</a>';
								}			
											  
					  			echo '</div>
							</div>';
					}
				?>
                </div>
            </div>
    </div>
</div>

<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(function(){
        $(".chat").niceScroll();
    })
</script>
<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        console.log("GPS Failed.");
    }
}

function showPosition(position) {
    console.log("Latitude: " + position.coords.latitude);
    console.log("Longitude: " + position.coords.longitude);
}

getLocation();
</script>
</body>
</html>
