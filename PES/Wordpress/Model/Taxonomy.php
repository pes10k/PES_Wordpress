<?php

class PES_Wordpress_Model_Taxonomy extends PES_Wordpress_Model {

  /**
   * A PDO statement for querying for taxonomy terms by name and taxonomy
   * type.  This is lazy loaded, so will be FALSE when not yet requested.
   *
   * @var PDOStatement|bool
   */
  private $sth_taxonomy_term_with_name = FALSE;

  /**
   * A PDO statement for querying for taxonomy terms by id and taxonomy
   * type.  This is lazy loaded, so will be FALSE when not yet requested.
   *
   * @var PDOStatement|bool
   */
  private $sth_taxonomy_term_with_id = FALSE;

  /**
   * A prepared statement for saving a new taxonomy term into the database.
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_term_save = FALSE;

  /**
   * A prepared statement that handles creating the needed relations for a
   * newly inserted / created taxonomy term
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_term_taxonomy_save = FALSE;

  /**
   * A prepared statement that handles associating a taxonomy term with post
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_associate_term_with_post = FALSE;

  /**
   * Returns an object representing a taxonomy term from the database with
   * the given name, if one exists.
   *
   * @param string $name
   *   The name of a taxonomy term
   * @param string $type
   *   The type of taxonomy to search in
   *
   * @return PES_Wordpress_Result_Taxonomy|bool
   *   An object representing a taxonomy term in the database, if one exists,
   *   otherwise FALSE
   */
  public function termWithName($name, $type = 'post_tag') {

    if ( ! $this->sth_taxonomy_term_with_name) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          ' . $connector->prefixedTable('terms') . '.*
        FROM
          ' . $connector->prefixedTable('terms') . '
        JOIN
          ' . $connector->prefixedTable('term_taxonomy') . ' USING (`term_id`)
        WHERE
          ' . $connector->prefixedTable('terms') . '.`name` = :name AND
          ' . $connector->prefixedTable('term_taxonomy') . '.`taxonomy` = :taxonomy_type
        LIMIT
          1';

      $this->sth_taxonomy_term_with_name = $connector->db()->prepare($prepared_query);
    }

    $this->sth_taxonomy_term_with_name->bindParam(':name', $name);
    $this->sth_taxonomy_term_with_name->bindParam(':taxonomy_type', $type);
    $this->sth_taxonomy_term_with_name->execute();

    $row = $this->sth_taxonomy_term_with_name->fetchObject();

    return $row ? new PES_Wordpress_Result_Taxonomy($this->wp(), $row) : FALSE;
  }

  /**
   * Returns an object representing a taxonomy term from the database with
   * the given id, if one exists.
   *
   * @param int $term_id
   *   The id of a taxonomy term
   * @param string $type
   *   The type of taxonomy to search in
   *
   * @return PES_Wordpress_Result_Taxonomy|bool
   *   An object representing a taxonomy term in the database, if one exists,
   *   otherwise FALSE
   */
  public function termWithId($term_id, $type = 'post_tag') {

    if ( ! $this->sth_taxonomy_term_with_id) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          ' . $connector->prefixedTable('terms') . '.*
        FROM
          ' . $connector->prefixedTable('terms') . '
        JOIN
          ' . $connector->prefixedTable('term_taxonomy') . ' USING (`term_id`)
        WHERE
          ' . $connector->prefixedTable('terms') . '.`term_id` = :term_id AND
          ' . $connector->prefixedTable('term_taxonomy') . '.`taxonomy` = :taxonomy_type
        LIMIT
          1';

      $this->sth_taxonomy_term_with_id = $connector->db()->prepare($prepared_query);
    }

    $this->sth_taxonomy_term_with_id->bindParam(':term_id', $term_id);
    $this->sth_taxonomy_term_with_id->bindParam(':taxonomy_type', $type);
    $this->sth_taxonomy_term_with_id->execute();

    $row = $this->sth_taxonomy_term_with_id->fetchObject();

    return $row ? new PES_Wordpress_Result_Taxonomy($this->wp(), $row) : FALSE;
  }

  /**
   * Saves a new taxonomy term to the database.  The values array should be an array
   * with the following keys provided:
   *   - name (string):      The name of the term
   *   - slug (string):      A url equivilent of the above
   *   - taxonomy (string):  The type of term being saved, such as "post_tag" or
   *                         category
   *   - parent_term (int):  An optional id of a taxonomy term that is the
   *                         parent of the one being saved, in a tree-style
   *                         taxonomy
   *
   * @param array $values
   *   An arary of key-values matching the above description
   *
   * @return PES_Wordpress_Result_Taxonomy|FALSE
   *   Returns the newly created taxonomy object on success.  Otherwise FALSE
   */
  public function save($values) {

    if ( ! $this->sth_term_save) {

      $connector = $this->connector();
      $db = $connector->db();

      $save_term_query = '
        INSERT INTO
          ' . $connector->prefixedTable('terms') . '
          (name,
          slug)
        VALUES
          (:name,
          :slug)
      ';

      $this->sth_term_save = $db->prepare($save_term_query);

      $save_relation_query = '
        INSERT INTO
          ' . $connector->prefixedTable('term_taxonomy') . '
          (term_id,
          taxonomy,
          parent)
        VALUES
          (:term_id,
          :taxonomy,
          :parent)
      ';

      $this->sth_term_taxonomy_save = $db->prepare($save_relation_query);
    }

    $this->sth_term_save->bindParam(':name', $values['name']);
    $this->sth_term_save->bindParam(':slug', $values['slug']);

    if ( ! $this->sth_taxonomy_term_with_id->execute()) {

      return FALSE;
    }
    else {

      $db = $this->connector()->db();
      $term_id = $db->lastInsertId();

      $this->sth_term_taxonomy_save->bindParam(':term_id', $term_id);
      $this->sth_term_taxonomy_save->bindParam(':taxonomy', $values['taxonomy']);
      $this->sth_term_taxonomy_save->bindParam(':parent', empty($values['parent']) ? 0 : $values['parent']);
      $this->sth_term_taxonomy_save->execute();

      return $this->termWithId($db->lastInsertId(), $values['taxonomy']);
    }
  }

  /**
   * Associated a wordpress post with a term
   *
   * @param int $post_id
   *   The unique identifier of a wordpress post in the current install
   * @param int $term_id
   *   The unique identifier of a wordpress term in the current install
   *
   * @return bool
   *   TRUE if an association was form.  FALSE on any error
   */
  public function associatePostWithTerm($post_id, $term_id) {

    if ( ! $this->sth_associate_term_with_post) {

      $connector = $this->connector();
      $db = $connector->db();

      $associate_post_query = '
        INSERT INTO
          ' . $connector->prefixedTable('term_relationships') . '
          (object_id,
          term_taxonomy_id)
        VALUES
          (:post_id,
          :term_id)
      ';

      $this->sth_associate_term_with_post = $db->prepare($associate_post_query);
    }

    $this->sth_associate_term_with_post->bindParam(':post_id', $post_id);
    $this->sth_associate_term_with_post->bindParam(':term_id', $term_id);
    return $this->sth_associate_term_with_post->execute();
  }

  /**
   * Wordpress keeps a seperate count of the number of posts that have been
   * assigned to a given taxonomy term.  Calling this method updates these
   * counts incase something has fallen out of sync.
   */
  public function updateTaxonomyCounts() {

    $connector = $this->connector();

    $connector->db()->exec('
      UPDATE
        ' . $connector->prefixedTable('term_taxonomy') . ' AS tt
      SET
        tt.`count` = (
          SELECT
            COUNT(*)
          FROM
            ' . $connector->prefixedTable('term_relationships') . ' AS tr
          WHERE
            tr.`term_taxonomy_id` = tt.`term_taxonomy_id`
        )
      ');
  }
}