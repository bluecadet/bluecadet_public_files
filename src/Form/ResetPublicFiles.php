<?php

namespace Drupal\bluecadet_public_files\Form;

use Drupal\bluecadet_public_files\DatabaseTrait;
use Drupal\bluecadet_public_files\QueueTrait;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Messenger\MessengerTrait;

/**
 * Form to reset Public Files data.
 */
class ResetPublicFiles extends FormBase {

  use LoggerChannelTrait;
  use MessengerTrait;
  use DatabaseTrait;
  use QueueTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'reset_public_files';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['reset'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset Data'),
    ];

    return $form;
  }

  // phpcs:disable

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Delete it all.
    $this->getDatabase()->delete('bluecadet_public_files', [])->execute();

    $queue_factory = $this->getQueueFactory();
    $dir_queue = $queue_factory->get('scan_public_files_dir');

    // Queue Public Directory.
    $item = new \stdClass();
    $item->dir = 'public://';
    $dir_queue->createItem($item);

    // Send message.
    $this->messenger()->addMessage('You have reset the data.');
  }

}
