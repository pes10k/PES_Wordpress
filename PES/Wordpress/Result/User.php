<?php

/**
 * Instances of this class represent individual users in the current wordpress
 * install
 */
class PES_Wordpress_Result_User extends PES_Wordpress_Result {

  /**
   * Returns the unique identifier for the user record
   *
   * @return int
   *   An integer greater than zero, that uniquely represents the user in the
   *   current install.
   */
  public function id() {
    return $this->property('ID');
  }

  /**
   * Returns the login username the current user would use to log into wordpress
   *
   * @return string
   */
  public function login() {
    return $this->property('user_login');
  }

  /**
   * Returns the hashed password for the current user
   *
   * @return string
   */
  public function password() {
    return $this->property('user_pass');
  }

  /**
   * Returns the human-readable display name for the user
   *
   * @return string
   */
  public function displayName() {
    return $this->property('display_name');
  }
}