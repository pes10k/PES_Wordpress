<?php

abstract class PES_Wordpress_Result {

  /**
   * A reference to the shared PES_Wordpress object
   *
   * @var PES_Wordpress
   */
  private $wp;

  /**
   * Stores an object representing a row from the wordpress database
   *
   * @var stdClass
   */
  private $row;

  /**
   * Stores a reference to a wordpress database connector on instantiation
   * and an object representing a database row
   *
   * @param PES_Wordpress_Connector $connector
   *   A reference to an wordpress database connector
   * @param stdClass $row
   *   An object representing a database row
   */
  public function __construct(PES_Wordpress $wp, $row = FALSE) {
    $this->wp = $wp;
    $this->row = $row ?: new stdClass();
  }

  /**
   * Returns a reference to the shared root WP object, to handle
   * communicating between results and models.
   *
   * @return PES_Wordpres
   */
  protected function wp() {
    return $this->wp;
  }

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