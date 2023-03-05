<?php

namespace Drupal\bluecadet_public_files;

/**
 * Utility Trait to get Queue Factory.
 */
trait QueueTrait {

  /**
   * The queue factory.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * Gets the QueueFactory.
   *
   * @return \Drupal\Core\Queue\QueueFactory
   *   The queue factory.
   */
  protected function getQueueFactory() {
    if (!$this->queueFactory) {
      $this->queueFactory = \Drupal::service('queue');
    }
    return $this->queueFactory;
  }

}
