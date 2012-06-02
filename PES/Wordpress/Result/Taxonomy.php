<?php

class PES_Wordpress_Result_Taxonomy extends PES_Wordpress_Result {

  // ===================
  // ! Getter / Setters
  // ===================

  /**
   * Returns the unique identifier for a taxonomy term
   *
   * @return int|bool
   *   The unique identifier for a taxonomy term, or FALSE if not available
   */
  public function termId() {
    return $this->property('term_id');
  }

  /**
   * Returns the name of a taxonomy term
   *
   * @return string
   *   The name of the taxonomy term, or FALSE if not available
   */
  public function name() {
    return $this->property('name');
  }

  /**
   * Returns the slug for the taxonomy term
   *
   * @return string
   *   The slug for the taxonomy term, or FALSE if not available
   */
  public function slug() {
    return $this->property('slug');
  }
}