<?php
DEFINE ('db_user', 'root');
DEFINE ('db_password', 'JBqeOVDlSpx4kzdlm3uH');
DEFINE ('db_host', 'localhost');
DEFINE ('db_name', 'fugs');
class Database
{
	private $db_connection = null;

	public function __construct()	
	{
		$this->db_connection = mysqli_connect (db_host, db_user, db_password, db_name) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );
	}

	public function getHomepagePosts($user_location)
	{
		$posts = array();

		//$posts_query = "SELECT * FROM `parent_post` WHERE `time_of_post` >= DATE_SUB(NOW(), INTERVAL 5 DAY)  ORDER BY 'rating' LIMIT 100";	
		$posts_query = "SELECT * FROM `parent_post` WHERE `time_of_post` >= DATE_SUB(NOW(), INTERVAL 5 DAY)  ORDER BY `time_of_post` DESC LIMIT 100";	
		$posts_results = mysqli_query($this->db_connection, $posts_query);


		while ($row = mysqli_fetch_array($posts_results, MYSQLI_ASSOC))
		{
		  	$users_stmt = mysqli_stmt_init($this->db_connection);
			mysqli_stmt_prepare($users_stmt ,"SELECT * FROM `users` WHERE `user_id` = ?");
			mysqli_stmt_bind_param($users_stmt, "s", $row['user_id']);
			mysqli_stmt_execute($users_stmt);
			$users_results = mysqli_stmt_get_result($users_stmt);
			$users_rows = mysqli_fetch_array($users_results, MYSQLI_ASSOC);
			mysqli_stmt_close($users_stmt);

			$user = new User($users_rows['user_id'], $users_rows['user_name']);	
			$post = new Post($row['post_id'], $user, $row['post_content'], $row['post_subject'], $row['time_of_post'], $row['location_of_post'], $row['rating']);
			array_push($posts, $post);
		}

		return $posts;
	}

	public function makeNewPost($user_name, $post_title, $post_content, $post_location)
	{
	  $user_id = $this->getUserId($user_name);
	  $stmt = mysqli_stmt_init($this->db_connection);
	  mysqli_stmt_prepare($stmt, "INSERT INTO `parent_post` (`user_id`, `post_subject`, `post_content`, `time_of_post`, `rating`, `location_of_post`) VALUES (?,?,?,NOW(),0,?)");

	  mysqli_stmt_bind_param($stmt, 'ssss', $user_id, $post_title, $post_content, $post_location);

	  mysqli_stmt_execute($stmt); 
	  mysqli_stmt_close($stmt);
	}

	public function makeNewComment($user_id, $parent_post_id, $post_content)
	{
	  $stmt = mysqli_stmt_init($this->db_connection);
	  mysqli_stmt_prepare($stmt, "INSERT INTO `child_post`(`parent_post_id`, `post_content`, `user_id`) VALUES (?,?,?)");

	  mysqli_stmt_bind_param($stmt, 'sss', $parent_post_id, $post_content, $user_id);

	  mysqli_stmt_execute($stmt);
	  mysqli_stmt_close($stmt);

	}

	public function getComments($post_id)
	{
		$posts = array();
		$posts_query = "SELECT * FROM `child_post` WHERE `parent_post_id` = ? ORDER BY 'post_id'";	
		$stmt = mysqli_stmt_init($this->db_connection);
		
		mysqli_stmt_prepare($stmt, $posts_query);
		mysqli_stmt_bind_param($stmt, "s", $post_id);
		mysqli_stmt_execute($stmt);
		
		$post_results = mysqli_stmt_get_result($stmt);
		mysqli_stmt_close($stmt);

		while ($row = mysqli_fetch_array($post_results, MYSQLI_ASSOC))
		{
		  	$users_stmt = mysqli_stmt_init($this->db_connection);
			mysqli_stmt_prepare($users_stmt ,"SELECT * FROM `users` WHERE `user_id` = ?");
			mysqli_stmt_bind_param($users_stmt, "s", $row['user_id']);
			mysqli_stmt_execute($users_stmt);
			$users_results = mysqli_stmt_get_result($users_stmt);
			$users_rows = mysqli_fetch_array($users_results, MYSQLI_ASSOC);
			mysqli_stmt_close($users_stmt);

			$user = new User($users_rows['user_id'], $users_rows['user_name']);	
			$post = new Comment($user, $row['post_content']);
			array_push($posts, $post);
		}

		return $posts;
	}

	public function rateUpPost($post_id)
	{
		$stmt = mysqli_stmt_init($this->db_connection);
		mysqli_stmt_prepare($stmt, "UPDATE parent_post SET `rating` = `rating` + 1 WHERE `post_id` = ?");
		mysqli_stmt_bind_param($stmt, "s", $post_id);

		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}

	public function rateDownPost($post_id)
	{
		$stmt = mysqli_stmt_init($this->db_connection);
		mysqli_stmt_prepare($stmt, "UPDATE parent_post SET `rating` = `rating` - 1 WHERE `post_id` = ?");
		mysqli_stmt_bind_param($stmt, "s", $post_id);

		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}

	public function getUserId($user_name)
	{
		$stmt = mysqli_stmt_init($this->db_connection);
		mysqli_stmt_prepare($stmt, "SELECT `user_id` FROM `users` WHERE `user_name` = ?");
		mysqli_stmt_bind_param($stmt, "s", $user_name);
  		mysqli_stmt_execute($stmt);		
		$result = mysqli_stmt_get_result($stmt);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['user_id'];	
	}

	public function reportPost($post_id, $reported_user, $report_reason)
	{
		$stmt = mysqli_stmt_init($this->db_connection);
		mysqli_stmt_prepare($stmt, "INSERT INTO reportedPosts (post_id, reported_user, report_time, report_reason) VALUES (?, ?, NOW(), ?)");
		mysqli_stmt_bind_param($stmt, "sss", $post_id, $reported_user, $report_reason);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}

	public function getReportedPosts()
	{
	    $reported_posts = array();
		$stmt = mysqli_stmt_init($this->db_connection);
		mysqli_stmt_prepare($stmt, "SELECT * FROM reportedPosts ORDER BY report_time DESC");
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$post = new ReportedPost($row['post_id'], $row['report_reason'], $row['report_time'], $row['reported_user']);		
			array_push($reported_posts, $post);
		}
		mysqli_stmt_close($stmt);		
		return $reported_posts;
	}

	public function isUserAdmin($user_name)
	{
		$stmt = mysqli_stmt_init($this->db_connection);
		mysqli_stmt_prepare($stmt, "SELECT isadmin FROM users WHERE user_name = ?");
		mysqli_stmt_bind_param($stmt, "s", $user_name);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = mysqli_fetch_array($stmt, MYSQLI_ASSOC);
		mysqli_stmt_close($stmt);
		return $row['isadmin'];	
	}


}

?>
