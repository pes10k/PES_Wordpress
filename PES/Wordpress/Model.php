<?php

abstract class PES_Wordpress_Model {

  /**
   * A refernece to the central wordpress object that manages all models
   *
   * @var PES_Wordpress
   */
  private $wp;

  /**
   * Stores a reference to a wordpress database connector on instantiation
   *
   * @param PES_Wordpress $wp
   *   A reference to the global wordpress database connector
   */
  public function __construct(PES_Wordpress $wp) {
    $this->wp = $wp;
  }

  /**
   * Returns a reference to the shared database connector object
   *
   * @return PES_Wordpress_Connector
   */
  protected function connector() {
    return $this->wp->connector();
  }

  /**
   * Returns a reference to the shared core wordpress object, so that models
   * can load up other models.
   *
   * @return PES_Wordpress
   */
  protected function wordpress() {
    return $this->wp;
  }
}