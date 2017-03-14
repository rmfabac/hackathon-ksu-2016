<?php

class Post
{
  private $post_id = 0;

  // This is a User object
  private $user = null;

  private $message = null;

  private $title = null;
  
  private $date_posted = null;

  private $location = null;

  private $rating = 0;

  public function __construct($id, $u, $msg, $t, $date, $loc, $rat)
  {
	$this->post_id = $id;
  	$this->user = $u;
	$this->message = $msg;
	$this->title = $t;
	$this->date_posted = $date;
	$this->location = $loc;
	$this->rating = $rat;
  }

  public function getId()
  {
  	return $this->post_id;
  }

  public function getUser()
  {
  	return $this->user;
  }

  public function getMessage()
  {
  	return $this->message;
  }

  public function getTitle()
  {
  	return $this->title;
  }

  public function getDatePosted()
  {
  	return $this->date_posted;
  }
  
  public function getDateDiff()
  {
	$postDate = new DateTime($this->date_posted);
	$currentDateArr = getdate();
	$currentDate = new DateTime($currentDateArr['year'] . "-" . $currentDateArr['mon'] . "-" . $currentDateArr['mday'] . " " . $currentDateArr['hours'] . ":" . $currentDateArr['minutes'] . ":" . $currentDateArr['seconds']);
	$dateDiff = $postDate->diff($currentDate);
	
	if($dateDiff->d > 0)
		$dateDiffStr = $dateDiff->format("posted %a days ago...");
	else if($dateDiff->i > 0)
		$dateDiffStr = $dateDiff->format("posted %i minutes ago...");
	else
		$dateDiffStr = $dateDiff->format("posted %s seconds ago...");
	
  	return $dateDiffStr;
  }

  public function getLocation()
  {
  	return $this->location;
  }

  public function getRating()
  {
  	return $this->rating;
  }
}

?>
