<?php

namespace Drupal\bluecadet_public_files\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

/**
 *
 */
class ResetPublicFiles extends FormBase {
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
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['reset'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Reset Data'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $db = \Drupal::database();

    // Delete it all.
    $db->delete('bluecadet_public_files', [])->execute();

    $queue_factory = \Drupal::service('queue');
    $dir_queue = $queue_factory->get('scan_public_files_dir');

    // Queue Public Directory.
    $item = new \stdClass();
    $item->dir = 'public://';
    $dir_queue->createItem($item);

    drupal_set_message('You have reset the data.');
  }
}