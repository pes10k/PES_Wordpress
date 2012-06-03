<?php

class PES_Wordpress_Connector {

  /**
   * The prefix that should be used when connecting to wordpress tables.
   *
   * @var string
   */
  private $table_prefix = 'wp_';

  /**
   * The database user that should be used when connecting to the wordpress
   * database.
   *
   * @var string
   */
  private $db_user = '';

  /**
   * The password that should be used when connecting to the wordpress database.
   *
   * @var string
   */
  private $db_password = '';

  /**
   * The host that should be used when connecting the wordpress database.
   *
   * @var string
   */
  private $db_host = 'localhost';

  /**
   * The name of the database that contains the wordpress information
   *
   * @var string
   */
  private $db_name;

  /**
   * The established connection to the database, or FALSE if no connection
   * has been established.  This is lazyloaded for convenience
   *
   * @var PDO|bool
   */
  private $db = FALSE;

  /**
   * Returns the current PDO connection to the database if one has already
   * been established.  If one hasn't been established, attempts to create
   * a connection and returns that result.
   *
   * @return PDO
   *   A reference to an PDO connection to the wordpress database
   */
  public function db() {

    if ( ! $this->db) {

      $this->db = new PDO(
        'mysql:dbname=' . $this->db_name . ';host=' . $this->db_host . ';charset=UTF-8',
        $this->db_user,
        $this->db_password,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
      );
    }

    return $this->db;
  }

  /**
   * Returns the name of a table in the database with the given name,
   * with the wordpress set table prefix infront of it.
   *
   * @param string $table_name
   *   The name of a table in the database, without the wordpress prefix
   *
   * @return string
   *   The prefixed table name
   */
  public function prefixedTable($table_name) {
    return '`' . $this->table_prefix . $table_name . '`';
  }

  // ==========
  // ! Setters
  // ==========

  /**
   * Sets the table prefix used when connecting to wordpress tables.
   *
   * @param string $a_prefix
   *   The prefix, such as 'wp_' that exists before table names
   *
   * @return PES_Wordpress_Connector
   *   A reference to the current object, to allow for method chaining.
   */
  public function setPrefix($a_prefix) {
    $this->table_prefix = $a_prefix;
    return $this;
  }

  /**
   * Sets the database username used when establishing a connection
   *
   * @param string $a_user_name
   *   The name of a database user
   *
   * @return PES_Wordpress_Connector
   *   A reference to the current object, to allow for method chaining.
   */
  public function setDbUsername($a_user_name) {
    $this->db_user = $a_user_name;
    return $this;
  }

  /**
   * Sets the name of the host for the database where the wordpress
   * information is
   *
   * @param string $a_host_name
   *   The hostname for the wordpress database
   *
   * @return PES_Wordpress_Connector
   *   A reference to the current object, to allow for method chaining.
   */
  public function setDbHost($a_host_name) {
    $this->db_host = $a_host_name;
    return $this;
  }

  /**
   * Sets the password that should be used when connecting to the database
   *
   * @param string $a_password
   *   The database password
   *
   * @return PES_Wordpress_Connector
   *   A reference to the current object, to allow for method chaining.
   */
  public function setDbPassword($a_password) {
    $this->db_password = $a_password;
    return $this;
  }

  /**
   * Sets the name of the database that contains the wordpress information
   *
   * @param string $a_name
   *   The name of a database
   *
   * @return PES_Wordpress_Connector
   *   A reference to the current object, to allow for method chaining.
   */
  public function setDbName($a_name) {
    $this->db_name = $a_name;
    return $this;
  }
}