<?php

namespace Drupal\bluecadet_public_files;

/**
 * Utility Trait to get Databse Connection.
 */
trait DatabaseTrait {

  /**
   * Database Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * Gets the Database Service.
   *
   * @return \Drupal\Core\Database\Connection
   *   The Database connection.
   */
  protected function getDatabase() {
    if (!$this->database) {
      $this->database = \Drupal::service('database');
    }
    return $this->database;
  }

}
