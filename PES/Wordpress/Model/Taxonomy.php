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
            ' . $connector->prefixedTable('term_relationships') . '
          WHERE
            ' . $connector->prefixedTable('term_relationships') . '.`term_taxonomy_id` = tt.`term_taxonomy_id`
        )
      ');
  }
}