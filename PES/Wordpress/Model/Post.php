<?php

class PES_Wordpress_Model_Post extends PES_Wordpress_Model {

  /**
   * A prepared statement for fetching a post by its post id.
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_post_with_id = FALSE;

  /**
   * A prepared statement for fetching a post by its title, status and type.
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_post_with_title_status_and_type = FALSE;

  /**
   * Returns the post with the given post id, if one exists.
   *
   * @param int $post_id
   *   The unique id of a post in the database
   *
   * @return PES_Wordpress_Result_Post|FALSE
   *   Returns an object representing the post, if one exists.  Otherwise,
   *   returns FALSE
   */
  public function postWithId($post_id) {

    if ( ! $this->sth_post_with_id) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          ' . $connector->prefixedTable('posts') . '.*
        FROM
          ' . $connector->prefixedTable('posts') . '
        WHERE
          ID = :post_id
        LIMIT
          1';

      $this->sth_post_with_id = $connector->db()->prepare($prepared_query);
    }

    $this->sth_post_with_id->bindParam(':post_id', $post_id);
    $this->sth_post_with_id->execute();

    $row = $this->sth_post_with_id->fetchObject();

    return $row ? new PES_Wordpress_Result_Post($this->wp(), $row) : FALSE;
  }

  /**
   * Searches to see if there are any posts in the database with the given
   * title, status and type
   *
   * @param string $title
   *   The possible title of a wordpress post
   * @param string $status
   *   The type of wordpress post status to search by, such as 'published'
   *   or 'draft'
   * @param string $type
   *   The type of post to search by, such as 'post' or a custom post type
   *
   * @return array
   *   Returns an array of zero or more PES_Wordpress_Result_Post objects,
   *   ordered by least to most recent.
   */
  public function postsWithTitleStatusAndType($title, $status = 'published', $type = 'post') {

    if ( ! $this->sth_post_with_title_status_and_type) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          ' . $connector->prefixedTable('posts') . '.*
        FROM
          ' . $connector->prefixedTable('posts') . '
        WHERE
          post_title = :title AND
          post_status = :status AND
          post_type = :type
        ORDER BY
          post_date
      ';

      $this->sth_post_with_title_status_and_type = $connector->db()->prepare($prepared_query);
    }

    $this->sth_post_with_title_status_and_type->bindParam(':title', $title);
    $this->sth_post_with_title_status_and_type->bindParam(':status', $status);
    $this->sth_post_with_title_status_and_type->bindParam(':type', $type);
    $this->sth_post_with_title_status_and_type->execute();

    $posts = array();

    while ($row = $this->sth_post_with_title_status_and_type->fetchObject()) {

      $posts[] = new PES_Wordpress_Result_Post($this->wp(), $row);
    }

    return $posts;
  }
}