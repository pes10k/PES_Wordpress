<?php

namespace PES\Wordpress\Model;
use \PES\Wordpress\Result as Result;

class User extends \PES\Wordpress\Model {

  /**
   * A prepared statement for fetching a user by its unique id.
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_user_with_id;

  /**
   * A prepared statement for fetching a user by its login name.
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_user_with_login;

  /**
   * A prepared statement for fetching a user by its human readable, display name.
   * This is lazy loaded, so it starts as FALSE and only is created when needed.
   */
  private $sth_user_with_display_name;

  /**
   * Fetches an user with a given unique id
   *
   * @param int $user_id
   *   The unique identifier for a wordpress user
   *
   * @return \PES\Wordpress\Result\User|FALSE
   *   Returns an object representing a user in the wordpress install,
   *   or FALSE if no matching user could be found.
   */
  public function userWithId($user_id) {

    if ( ! $this->sth_user_with_id) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          *
        FROM
          ' . $connector->prefixedTable('users') . '
        WHERE
          ID = :user_id
        LIMIT
          1
      ';

      $this->sth_user_with_id = $connector->db()->prepare($prepared_query);
    }

    $this->sth_user_with_id->bindParam(':user_id', $user_id);
    $this->sth_user_with_id->execute();

    $row = $this->sth_user_with_id->fetchObject();
    return $row ? new Result\User($this->wp(), $row) : FALSE;
  }

  /**
   * Fetches an user with a given login name (ex bsimpson)
   *
   * @param string login
   *   The unique login name for a wordpress user
   *
   * @return \PES\Wordpress\Result\User|FALSE
   *   Returns an object representing a user in the wordpress install,
   *   or FALSE if no matching user could be found.
   */
  public function userWithLoginName($login) {

    if ( ! $this->sth_user_with_login) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          *
        FROM
          ' . $connector->prefixedTable('users') . '
        WHERE
          user_login = :login
        LIMIT
          1
      ';

      $this->sth_user_with_login = $connector->db()->prepare($prepared_query);
    }

    $this->sth_user_with_login->bindParam(':login', $login);
    $this->sth_user_with_login->execute();

    $row = $this->sth_user_with_login->fetchObject();
    return $row ? new Result\User($this->wp(), $row) : FALSE;
  }

  /**
   * Fetches a user with a given human readable, display name (ex Bart Simpson)
   *
   * @param string $display_name
   *   The human readable, unique display name for a user
   *
   * @return array
   *   Returns an array of zero or more user objects that have the given display
   *   name.
   */
  public function userWithDisplayName($display_name) {

    if ( ! $this->sth_user_with_display_name) {

      $connector = $this->connector();

      $prepared_query = '
        SELECT
          *
        FROM
          ' . $connector->prefixedTable('users') . '
        WHERE
          display_name = :display_name
        LIMIT
          1
      ';

      $this->sth_user_with_display_name = $connector->db()->prepare($prepared_query);
    }

    $this->sth_user_with_display_name->bindParam(':display_name', $display_name);
    $this->sth_user_with_display_name->execute();

    $row = $this->sth_user_with_display_name->fetchObject();
    return $row ? new Result\User($this->wp(), $row) : FALSE;
  }

  /* ********************************* */
  /* ! Abstract Model implementations  */
  /* ********************************* */

  /**
   * Returns a populated user result object, with the user id matching the given
   * unique id.
   *
   * @param int $id
   *   The unique id of a user
   *
   * @return \PES\Wordpress\Result\User|FALSE
   *   Either a populated user result object, if there is a user in the system
   *   that matches the given unique id.  Otherwise, FALSE.
   */
  public function get($id) {
    return $this->userWithId($id);
  }

  /**
   * @todo Implement user delete method
   */
  public function delete($id) {}
}
