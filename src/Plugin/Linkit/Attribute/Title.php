<?php

/**
 * @file
 * Contains \Drupal\linkit\Plugin\Linkit\Attribute\Title.
 */

namespace Drupal\linkit\Plugin\Linkit\Attribute;

use Drupal\linkit\AttributeBase;

/**
 * Title attribute.
 *
 * @Attribute(
 *   id = "title",
 *   label = @Translation("Title"),
 *   description = @Translation("Basic input field for the title attribute.")
 * )
 */
class Title extends AttributeBase {

  /**
   * {@inheritdoc}
   */
  public function buildFormElement($default_value) {
    return [
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#maxlength' => 255,
      '#size' => 40,
    ];
  }

}
