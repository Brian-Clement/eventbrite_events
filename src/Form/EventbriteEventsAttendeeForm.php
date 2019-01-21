<?php

namespace Drupal\eventbrite_events\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Eventbrite attendee edit forms.
 *
 * @ingroup eventbrite_events
 */
class EventbriteEventsAttendeeForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\eventbrite_events\Entity\EventbriteEventsAttendee */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Eventbrite attendee.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Eventbrite attendee.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.eventbrite_events_attendee.canonical', ['eventbrite_events_attendee' => $entity->id()]);
  }

}
