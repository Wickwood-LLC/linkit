<?php

/**
 * @file
 * Contains \Drupal\linkit\Form\Attribute\AddForm.
 */

namespace Drupal\linkit\Form\Attribute;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\linkit\AttributeManager;
use Drupal\linkit\ProfileInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to apply attributes to a profile.
 */
class AddForm extends FormBase {

  /**
   * The profiles to which the attributes will be applied.
   *
   * @var \Drupal\linkit\ProfileInterface
   */
  protected $linkitProfile;

  /**
   * The attribute manager.
   *
   * @var \Drupal\linkit\AttributeManager
   */
  protected $manager;

  /**
   * Constructs a new AddForm.
   *
   * @param \Drupal\linkit\AttributeManager $manager
   *   The attribute manager.
   */
  public function __construct(AttributeManager $manager) {
    $this->manager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.linkit.attribute')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "linkit_attribute_add_form";
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ProfileInterface $linkit_profile = NULL) {
    $this->linkitProfile = $linkit_profile;

    $header = array(
      'label' => $this->t('Attributes'),
      'description' => $this->t('Description'),
    );

    $form['plugins'] = array(
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $this->buildRows(),
      '#empty' => $this->t('No attribute available.'),
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Add attributes'),
      '#submit' => array('::submitForm'),
      '#tableselect' => TRUE,
      '#button_type' => 'primary',
    );
    $form['actions']['cancel'] = array(
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => $this->linkitProfile->urlInfo('attributes'),
      '#attributes' => ['class' => ['button']],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();

    foreach (array_filter($form_state->getValue('plugins')) as $plugin_id) {
      $plugin = array(
        'id' => $plugin_id,
      );
      $this->linkitProfile->addAttribute($plugin);
    }

    $this->linkitProfile->save();

    $form_state->setRedirectUrl($this->linkitProfile->urlInfo('attributes'));
  }

  /**
   * Builds the table rows.
   *
   * Only attributes that is not already applied to the profile are shown.
   *
   * @return array
   *   An array of table rows.
   */
  private function buildRows() {
    $rows = array();

    $applied_plugins = $this->linkitProfile->getAttributes()->getConfiguration();
    $all_plugins = $this->manager->getDefinitions();

    foreach (array_diff_key($all_plugins, $applied_plugins) as $definition) {
      /** @var \Drupal\linkit\AttributeInterface $plugin */
      $plugin = $this->manager->createInstance($definition['id']);

      $row = array(
        'label' => (string) $plugin->getLabel(),
        'description' => (string) $plugin->getDescription(),
      );

      $rows[$plugin->getPluginId()] = $row;
    }

    return $rows;
  }

}
