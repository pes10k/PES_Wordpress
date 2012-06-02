<?php

class PES_Wordpress_Result {

  /**
   * A reference to a PES_Wordpress_Connector object that handles connections
   * to the database
   *
   * @var PES_Wordpress_Connector
   */
  private $connector;

  /**
   * Stores a reference to a wordpress database connector on instantiation
   * and an object representing a database row
   *
   * @param PES_Wordpress_Connector $connector
   *   A reference to an wordpress database connector
   * @param stdClass $row
   *   An object representing a database row
   */
  public function __construct(PES_Wordpress_Connector $connector, $row = FALSE) {
    $this->connector = $connector;
    $this->row = $row ?: new stdClass();
  }

  /**
   * Stores an object representing a row from the wordpress database
   *
   * @var stdClass
   */
  private $row;

  /**
   * Returns the given property on the current result object if it exists,
   * or otherwise returns FALSE
   *
   * @param string $name
   *   The database column that stores the requested property
   *
   * @return string|bool
   *   Returns the value of the requested property if it exists, or otherwise
   *   FALSE
   */
  protected function property($name) {

    return isset($this->row->$name) ? $this->row->$name : FALSE;
  }
}