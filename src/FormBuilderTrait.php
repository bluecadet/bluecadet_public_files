<?php

namespace Drupal\bluecadet_public_files;

/**
 * Utility Trait to get the FormBuilder.
 */
trait FormBuilderTrait {

  /**
   * The queue factory.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * Gets Drupal Form builder.
   */
  protected function getFormBuilder() {
    if (!$this->formBuilder) {
      $this->formBuilder = \Drupal::formBuilder();
    }
    return $this->formBuilder;
  }

}
