<?php

namespace PES\Wordpress\Result;

class Comment extends \PES\Wordpress\Result {

  /**
   * Returns the uniquely identifying id for a comment
   *
   * @return int
   */
  public function id() {
    return $this->property('comment_ID');
  }

  /**
   * Returns an object representing the post that this comment belongs to,
   * if one exists
   *
   * @return \PES\Wordpress\Result\Post|FALSE
   */
  public function post() {
    $post_id = $this->property('comment_post_ID');
    return $post_id ? $this->wp()->postModel()->postWithId($post_id) : FALSE;
  }

  /**
   * Returns the provided author name for the comment (note that this is not
   * related to wordpress users.)
   *
   * @return string
   */
  public function author() {
    return $this->property('comment_author');
  }

  /**
   * Returns the provided email address for the author of the comment
   *
   * @return string
   */
  public function email() {
    return $this->property('comment_author_email');
  }

  /**
   * Returns the date and time that the comment was added, in the configured
   * timezone
   *
   * @return DateTime
   */
  public function date() {
    $date = $this->property('comment_date');
    return $date ? new DateTime($date, $this->wp()->timezone()) : FALSE;
  }

  /**
   * Returns the body of the comment
   *
   * @return string
   */
  public function content() {
    return $this->property('comment_content');
  }
}
