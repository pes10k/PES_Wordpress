<?php

namespace PES\Wordpress;

abstract class Model {

  /**
   * A refernece to the central wordpress object that manages all models
   *
   * @var PES_Wordpress
   */
  private $wp;

  /**
   * Stores a reference to a wordpress database connector on instantiation
   *
   * @param \PES\Wordpress $wp
   *   A reference to the global wordpress database connector
   */
  public function __construct(\PES\Wordpress $wp) {
    $this->wp = $wp;
  }

  /**
   * Saves a new record to the database.  Inheriting models implement this to
   * handle managing and validating fields for the table(s) they manage.
   *
   * @param array $values
   *   Key-pair values for each column the model supports saving to
   *
   * @return mixed
   *   Returns the model's corresponding result object if a new record was
   *   created.  Otherwise, returns FALSE.
   */
//  abstract public function save($values);

  /**
   * Returns a reference to the shared database connector object
   *
   * @return \PES\Wordpress\Connector
   */
  protected function connector() {
    return $this->wp->connector();
  }

  /**
   * Returns a reference to the shared core wordpress object, so that models
   * can load up other models.
   *
   * @return \PES\Wordpress
   */
  protected function wp() {
    return $this->wp;
  }
}
