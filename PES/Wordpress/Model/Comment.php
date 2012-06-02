<?php

class PES_Wordpress_Model_Comment extends PES_Wordpress_Model {

  /**
   * A prepared statement for fetching a comment by its id.
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_comment_with_id = FALSE;

  /**
   * A prepared statement for fetching all comments associated with a post.
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_comments_for_post_id = FALSE;

  /**
   * Attempts to return a single comment for a post on a given date and time.
   * Useful when trying to see if a comment exists, but you don't have its unique
   * id.  This is lazy loaded, so it starts as FALSE and only is created when
   * needed.
   */
  private $sth_comment_by_author_for_post_and_date = FALSE;

  /**
   * Returns the comment with the given comment id, if one exists.
   *
   * @param int $comment_id
   *   The unique id of a comment in the database
   *
   * @return PES_Wordpress_Result_Comment|FALSE
   *   Returns an object representing the comment, if one exists.  Otherwise,
   *   returns FALSE
   */
  public function commentWithId($comment_id) {

    if ( ! $this->sth_comment_with_id) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          ' . $connector->prefixedTable('comments') . '.*
        FROM
          ' . $connector->prefixedTable('comments') . '
        WHERE
          comment_ID = :comment_id
        LIMIT
          1';

      $this->sth_comment_with_id = $connector->db()->prepare($prepared_query);
    }

    $this->sth_comment_with_id->bindParam(':comment_id', $comment_id);
    $this->sth_comment_with_id->execute();

    $row = $this->sth_comment_with_id->fetchObject();

    return $row ? new PES_Wordpress_Result_Comment($this->wp(), $row) : FALSE;
  }

  /**
   * Return all comments associated with a given post
   *
   * @param int $post_id
   *   The unique identifier of a post in the system
   *
   * @return array
   *   Returns an array of zero or more comment objects, ordered by comment
   *   date
   */
  public function commentsForPost($post_id) {

    if ( ! $this->sth_comments_for_post_id) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          ' . $connector->prefixedTable('comments') . '.*
        FROM
          ' . $connector->prefixedTable('comments') . '
        WHERE
          comment_post_ID = :post_id
        ORDER BY
          comment_date';

      $this->sth_comments_for_post_id = $connector->db()->prepare($prepared_query);
    }

    $this->sth_comments_for_post_id->bindParam(':post_id', $post_id);
    $this->sth_comments_for_post_id->execute();

    $comments = array();

    while ($row = $this->sth_comments_for_post_id->fetchObject()) {

      $comments[] = new PES_Wordpress_Result_Comment($this->wp(), $row);
    }

    return $comments;
  }

  /**
   * Returns any comments left by a given author for a particular post
   * at a particular time.  This is intended to be used when you want
   * to check if a comment exists, but you don't have a way of getting
   * at its unique id
   *
   * @param string $author_name
   *   The human readable name left for a comment
   * @param int $post_id
   *   The unique identifier for a post in the system
   * @param DateTime $time
   *   The time that the comment to search for was created.
   *
   * @return array
   *   Zero or more PES_Wordpress_Result_Comment objects that match the
   *   given parameters.
   */
  public function commentByAuthorForPostAtTime($author_name, $post_id, DateTime $time) {

    if ( ! $this->sth_comment_by_author_for_post_and_date) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          ' . $connector->prefixedTable('comments') . '.*
        FROM
          ' . $connector->prefixedTable('comments') . '
        WHERE
          comment_post_ID = :post_id AND
          comment_author = :author AND
          date = :date
        ORDER BY
          comment_date';

      $this->sth_comment_by_author_for_post_and_date = $connector->db()->prepare($prepared_query);
    }

    $this->sth_comment_by_author_for_post_and_date->bindParam(':author', $author_name);
    $this->sth_comment_by_author_for_post_and_date->bindParam(':post_id', $post_id);
    $this->sth_comment_by_author_for_post_and_date->bindParam(':date', $time->format('Y-m-d H:i:s'));
    $this->sth_comment_by_author_for_post_and_date->execute();

    $comments = array();

    while ($row = $this->sth_comment_by_author_for_post_and_date->fetchObject()) {

      $comments[] = new PES_Wordpress_Result_Comment($this->wp(), $row);
    }

    return $comments;
  }
}