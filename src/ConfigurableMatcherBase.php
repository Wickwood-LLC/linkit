<?php

/**
 * @file
 * Contains \Drupal\linkit\ConfigurableMatcherBase.
 */

namespace Drupal\linkit;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a base class for configurable matchers.
 *
 * @see plugin_api
 */
abstract class ConfigurableMatcherBase extends MatcherBase implements ConfigurableMatcherInterface {

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

}