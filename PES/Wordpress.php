<?php

class PES_Wordpress {

  /**
   * Reference to the database connector for the wordpress install
   *
   * @var PES_Wordpress_Connector|FALSE
   */
  private $connector = FALSE;

  /**
   * Reference to the lazily loaded post model, for fetching and saving
   * wordpress posts.
   *
   * @var PES_Wordpress_Model_Post|FALSE
   */
  private $post_model = FALSE;

  /**
   * Reference to the lazily loaded user model, for fetching and saving
   * information about wordpress users.
   *
   * @var PES_Wordpress_Model_User|FALSE
   */
  private $user_model = FALSE;

  /**
   * Reference to the lazily loaded taxonomy model, used for fetching
   * and saving information about taxonomies, tags, categories, etc
   * in the current wordpress install
   *
   * @var PES_Wordpress_Model_Taxonomy|FALSE
   */
  private $taxonomy_model = FALSE;

  /**
   * Reference to the lazily loaded comment model, used for fetching
   * and saving information about comments about posts
   * in the current wordpress install
   *
   * @var PES_Wordpress_Model_Comment|FALSE
   */
  private $comment_model = FALSE;

  /**
   * The timezone of the wordpress install.  If this isn't provided, the
   * timezone of the current server will be used.  This is lazy loaded
   * to, so we don't assume the default unless someone has tried to get
   * the timezone and nothing has yet been set.
   *
   * @var DateTimeZone|FALSE
   */
  private $timezone = FALSE;

  /**
   * Rebuilds several cached values in the wordpress system, such as the
   * counts for the number of comments per post and posts per term.
   * If any changes have been made to the wordpress database through
   * this libraries models, this should be called to make sure that
   * the system stays in sync.
   */
  public function cleanup() {
    $this->postModel()->updateCommentCounts();
    $this->taxonomyModel()->updateTaxonomyCounts();
  }

  // ===================
  // ! Getter / Setters
  // ===================

  /**
   * Returns an instantiated connector object.  Note that the caller should
   * then populate the returned connector object with the necessary database
   * parameters so that the models can connect to the wordpress database.
   *
   * @return PES_Wordpress_Connector
   *   An instantiated and shared reference to the connector object
   */
  public function connector() {

    if ( ! $this->connector) {

      $this->connector = new PES_Wordpress_Connector();
    }

    return $this->connector;
  }

  /**
   * Returns a shared refernece to a model object used for fetching and
   * creating posts in the wordpress install
   *
   * @return PES_Wordpress_Model_Post
   *   An shared reference to the post model
   */
  public function postModel() {

    if ( ! $this->post_model) {

      $this->post_model = new PES_Wordpress_Model_Post($this);
    }

    return $this->post_model;
  }

  /**
   * Returns a shared refernece to a model object used for fetching and
   * creating information about users in the current wordpress install
   *
   * @return PES_Wordpress_Model_User
   *   An shared reference to the user model
   */
  public function userModel() {

    if ( ! $this->user_model) {

      $this->user_model = new PES_Wordpress_Model_User($this);
    }

    return $this->user_model;
  }

  /**
   * Returns a shared refernece to a model object used for fetching and
   * creating information about taxonomies cand categorizations in the
   * current wordpress install
   *
   * @return PES_Wordpress_Model_Taxonomy
   *   An shared reference to the taxonomy model
   */
  public function taxonomyModel() {

    if ( ! $this->taxonomy_model) {

      $this->taxonomy_model = new PES_Wordpress_Model_Taxonomy($this);
    }

    return $this->taxonomy_model;
  }

  /**
   * Returns a shared reference to a model object used for fetching
   * and saving information about comments on posts in the current
   * wordpress install
   *
   * @return PES_Wordpress_Model_Comment
   *   A shared reference to the comment model
   */
  public function commentModel() {

    if ( ! $this->comment_model) {

      $this->comment_model = new PES_Wordpress_Model_Comment($this);
    }

    return $this->comment_model;
  }

  /**
   * Returns a reference to the timezone for the wordpress install data
   *
   * @return DateTimeZone
   */
  public function timezone() {

    if ( ! $this->timezone) {

      $this->timezone = new DateTimeZone(date_default_timezone_get());
    }

    return $this->timezone;
  }

  /**
   * Sets the timezone the system should use when interpreting dates in the
   * wordpress database
   *
   * @param DateTimeZone $time
   *   The timezone to use when interpreting dates in the database
   *
   * @reutrn PES_Wordpress
   *   A reference to the current object to allow for method chaining.
   */
  public function setTimezone(DateTimeZone $timezone) {
    $this->timezone = $timezone;
    return $this;
  }
}