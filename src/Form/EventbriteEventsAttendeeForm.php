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
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger->addMessage($this->t('Created the %label Eventbrite attendee.', [
          '%label' => $this->entity->label(),
        ]));
        break;

      default:
        $this->messenger->addMessage($this->t('Saved the %label Eventbrite attendee.', [
          '%label' => $this->entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.eventbrite_events_attendee.canonical', ['eventbrite_events_attendee' => $this->entity->id()]);
  }

}
