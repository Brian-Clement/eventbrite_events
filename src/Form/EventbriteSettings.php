<?php

namespace Drupal\eventbrite_events\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for the Eventbrite Events module.
 */
class EventbriteSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'eventbrite_events.eventbritesettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eventbrite_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('eventbrite_events.eventbritesettings');
    $form['eventbrite_oauth_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OAuth Token'),
      '#description' => $this->t('Enter your Eventbrite API token'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('eventbrite_oauth_token'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('eventbrite_events.eventbritesettings')
      ->set('eventbrite_oauth_token', $form_state->getValue('eventbrite_oauth_token'))
      ->save();
  }

}
