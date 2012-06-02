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
   * A prepared statement for saving a new post to the database.
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_save_post = FALSE;

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
          p.*
        FROM
          ' . $connector->prefixedTable('posts') . ' AS p
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
          p.*
        FROM
          ' . $connector->prefixedTable('posts') . ' AS p
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

  /**
   * Saves a new post term to the database.  The values array should be an array
   * with the following keys provided:
   *   - user_id (int):      The unique id of the wordpress user who created
   *                         this post
   *   - date (DateTime):    The date that the post was created
   *   - body (string):      The main body content of the post
   *   - title (string):     The title of the post
   *   - slug (string):      A url-friendly version of the post title
   *   - excerpt (string):   The teaser or excerpt of the post, if it exists
   *   - status (string):    The post's status, such as "publish" or "draft"
   *   - comment_status (bool):
   *                         Whether the post can still be commented on.
   *   - modified_date (DateTime):
   *                         The date that the post was last modified on
   *   - parent (int):       Optional unique identifier for a post that is the
   *                         parent of this post, such as if the current post
   *                         is a new draft version of another post
   *   - guid (string):      Globally unique identifier for the post, as a URL
   *   - type (string):      The type of post being saved.  Defaults to "post".
   *                         Other valid values depend on the configuration of the
   *                         wordpress install
   *
   * @param array $values
   *   An arary of key-values matching the above description
   *
   * @return PES_Wordpress_Result_Post|FALSE
   *   Returns the newly created post object on success.  Otherwise FALSE
   */
  public function save($values) {

    if ( ! $this->sth_save_post) {

      $connector = $this->connector();
      $db = $connector->db();

      $save_post_query = '
        INSERT INTO
          ' . $connector->prefixedTable('posts') . '
          (post_author,
          post_date,
          post_date_gmt,
          post_content,
          post_title,
          post_excerpt,
          post_status,
          comment_status,
          post_name,
          post_modified,
          post_modified_gmt,
          post_parent,
          guid,
          post_type)
        VALUES
          (:user_id,
          :date,
          :date_gmt,
          :content,
          :title,
          :excerpt,
          :status,
          :comment_status,
          :slug,
          :modified,
          :modified_gmt,
          :parent,
          :guid,
          :type)
      ';

      $this->sth_save_post = $db->prepare($save_post_query);
    }

    $this->sth_save_post->bindParam(':user_id', $values['user_id']);
    $this->sth_save_post->bindParam(':date', $values['date']->format('Y-m-d H:i:s'));
    $this->sth_save_post->bindParam(':date_gmt', gmdate('Y-m-d H:i:s', $values['date']->format('U')));
    $this->sth_save_post->bindParam(':content', $values['content']);
    $this->sth_save_post->bindParam(':title', $values['title']);
    $this->sth_save_post->bindParam(':excerpt', $values['excerpt']);
    $this->sth_save_post->bindParam(':status', $values['status']);
    $this->sth_save_post->bindParam(':comment_status', empty($values['comment_status']) ? 'closed' : 'open');
    $this->sth_save_post->bindParam(':slug', $values['slug']);
    $this->sth_save_post->bindParam(':modified', $values['modified']->format('Y-m-d H:i:s'));
    $this->sth_save_post->bindParam(':modified_gmt', gmdate('Y-m-d H:i:s', $values['modified']->format('U')));
    $this->sth_save_post->bindParam(':parent', empty($values['parent']) ? 0 : $values['parent']);
    $this->sth_save_post->bindParam(':guid', $values['guid']);
    $this->sth_save_post->bindParam(':type', empty($values['type']) ? 'post' : $values['type']);

    if ( ! $this->sth_save_post->execute()) {

      return FALSE;
    }
    else {

      return $this->postWithId($this->connector()->db()->lastInsertId());
    }
  }

  /**
   * Wordpress keeps a seperate count of the number of comments that have been
   * assigned to each post.  Calling this method updates these
   * counts incase something has fallen out of sync.
   */
  public function updateCommentCounts() {

    $connector = $this->connector();

    $connector->db()->exec('
      UPDATE
        ' . $connector->prefixedTable('posts') . ' AS p
      SET
        p.`comment_count` = (
          SELECT
            COUNT(*)
          FROM
            ' . $connector->prefixedTable('comments') . ' AS c
          WHERE
            c.`comment_post_ID` = p.ID`
        )
      ');
  }
}