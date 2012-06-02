<?php

abstract PES_Wordpress_Model {

  /**
   * A reference to a PES_Wordpress_Connector object that handles connections
   * to the database
   *
   * @var PES_Wordpress_Connector
   */
  private $connector;

  /**
   * Stores a reference to a wordpress database connector on instantiation
   *
   * @param PES_Wordpress_Connector $connector
   *   A reference to an wordpress database connector
   */
  public function __construct(PES_Wordpress_Connector $connector) {
    $this->connector = $connector;
  }
}