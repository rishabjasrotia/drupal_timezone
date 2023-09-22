<?php

namespace Drupal\drupal_timezone\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\drupal_timezone\ModuleConstants;
use Drupal\drupal_timezone\Services\TimezoneHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *  Timezone Configuration Form.
 */
class TimezoneConfigForm extends ConfigFormBase {

  /**
   * TimezoneHelper object.
   *
   * @var \Drupal\drupal_timezone\Services\TimezoneHelper
   */
  protected $timezoneHelper;

  /**
   * Constructs a TimezoneConfigForm object.
   *
   * @param \Drupal\drupal_timezone\Services\TimezoneHelper $timezoneHelper
   *   The TimezoneHelper service.
   */
  public function __construct(TimezoneHelper $timezoneHelper) {
    $this->timezoneHelper = $timezoneHelper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('drupal_timezone.timezonehelper'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'drupal_timezone_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [ModuleConstants::SETTINGS];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(ModuleConstants::SETTINGS);
    $form['country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Country'),
      '#required' => TRUE,
      '#description' => $this->t('Enter country name.'),
      '#default_value' => $config->get('country'),
    ];
    $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#required' => TRUE,
      '#description' => $this->t('Enter city name.'),
      '#default_value' => $config->get('city'),
    ];
    $form['timezone'] = [
      '#type' => 'select',
      '#title' => $this->t('Timezone'),
      '#required' => TRUE,
      '#options' => $this->timezoneHelper->getTimezoneList(),
      '#description' => $this->t('Select timezone.'),
      '#default_value' => $config->get('timezone'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config(ModuleConstants::SETTINGS)
      ->set('country', $form_state->getValue('country'))
      ->set('city', $form_state->getValue('city'))
      ->set('timezone', $form_state->getValue('timezone'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
