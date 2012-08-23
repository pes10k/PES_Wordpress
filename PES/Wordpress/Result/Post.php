<?php

namespace PES\Wordpress\Result;

class Post extends \PES\Wordpress\Result {

  /**
   * Stores a relationship between the current post and the given taxonomy
   * term.
   *
   * @param \PES\Wordpress\Result\Taxonomy $term
   *   An object representing a taxonomy term in the current wordpress install
   *
   * @return bool
   *   TRUE if an association was form.  FALSE on any error
   */
  public function tagWithTerm(Taxonomy $term) {
    $this->wp()->taxonomyModel()->associatePostWithTerm($this->id(), $term->id());
  }

  // ==========
  // ! Getters
  // ==========

  /**
   * Returns the unique identifier of the post
   *
   * @return int
   */
  public function id() {
    return $this->property('ID');
  }

  /**
   * Returns the user object representing the author of this post.
   *
   * @return \PES\Wordpress\Result\User|FALSE
   *   The user object of the posts author, or FALSE if that can't
   *   be found.
   */
  public function author() {
    $author_id = $this->property('post_author');
    return $author_id ? $this->wp()->userModel()->userWithId($author_id) : FALSE;
  }

  /**
   * Returns a date object describing when the post was created
   *
   * @return \DateTime
   *   An object representing the time the post was created in the
   *   wordpress install's timezone.
   */
  public function createdDate() {
    $post_date = $this->property('post_date');
    return $post_date ? new \DateTime($post_date, $this->wp()->timezone()) : FALSE;
  }

  /**
   * Returns the title for the current post
   *
   * @return string
   */
  public function title() {
    return $this->property('post_title');
  }

  /**
   * Returns the main body content of the post
   *
   * @return string
   */
  public function content() {
    return $this->property('post_content');
  }

  /**
   * Returns the excerpt or teaser of the post
   *
   * @return string
   */
  public function excerpt() {
    return $this->property('post_excerpt');
  }

  /**
   * Returns the status of the post, such as 'publish' or 'draft'
   *
   * @return string
   */
  public function status() {
    return $this->property('post_status');
  }

  /**
   * Returns the slug version of the post title (ex, for a post titled
   * "Brand New Post", this might be something like "brand-new-post")
   *
   * @return string
   */
  public function slug() {
    return $this->property('post_name');
  }

  /**
   * Returns a date object describing when the post was last modified
   *
   * @return \DateTime
   *   An object representing the time the post was last modified in the
   *   wordpress install's timezone.
   */
  public function modifiedDate() {
    $modified_date = $this->property('post_modified');
    return $modified_date ? new \DateTime($modified_date, $this->wp()->timezone()) : FALSE;
  }

  /**
   * Returns the type of post, such as "page," "post" or a custom type
   *
   * @return string
   */
  public function type() {
    return $this->property('post_type');
  }

  /**
   * Returns an object represeting the parent of this post, if one exists
   *
   * @return \PES\Wordpress\Result\Post|FALSE
   */
  public function parentPost() {
    $parent_post_id = $this->property('post_parent');
    return $parent_post_id ? $this->wp()->postModel()->postWithId($parent_post_id) : FALSE;
  }
}
