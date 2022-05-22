<?php

namespace Drupal\eventbrite_events;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\eventbrite_events\Entity\EventbriteEventsAttendee;

/**
 * Syncs event attendees via EventBrite API..
 */
class SyncAttendees {

  /**
   * The Eventbrite Events API service.
   *
   * @var \Drupal\eventbrite_events\Api
   */
  protected $eventbriteEventsApi;

  /**
   * The Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new SyncAttendees object.
   *
   * @param \Drupal\eventbrite_events\Api $eventbrite_events_api
   *   The EventBrite API service.
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The Entity Type Manager service.
   */
  public function __construct(Api $eventbrite_events_api, EntityTypeManager $entity_type_manager) {
    $this->eventbriteEventsApi = $eventbrite_events_api;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Create attendee entities for the given event.
   *
   * @param int $eventbrite_event_id
   *   The Eventbrite ID.
   * @param string $event_status
   *   String describing an event's status.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function sync($eventbrite_event_id, $event_status) {
    // If the event is not active, to not attempt to sync attendees.
    if ($this->activeEvent($event_status) == FALSE) {
      return;
    }

    // Define an array to capture attendees.
    $attendees = [];

    // No query parameters for now.
    $query = [];

    // Get the attendees response for the given event ID.
    $results = $this->eventbriteEventsApi->connect('get', "events/$eventbrite_event_id/attendees/", $query, []);

    if (!$results) {
      return;
    }

    // Iterate over each page of response results to load user data.
    foreach ($results as $result) {
      foreach ($result['attendees'] as $attendee) {
        $attendees[] = $attendee['profile']['name'];

        // Check for existing attendee node so we can update existing.
        $entity = EventbriteEventsAttendee::load($attendee['id']);

        if ($entity) {
          $entity->set('name', $attendee['profile']['name']);
          $entity->set('job_title', isset($attendee['profile']['job_title']) ? $attendee['profile']['job_title'] : '');
          $entity->set('company', isset($attendee['profile']['company']) ? $attendee['profile']['company'] : '');
          $entity->set('ticket_class_name', $attendee['ticket_class_name']);
          $entity->set('ticket_class_id', $attendee['ticket_class_id']);
          $entity->set('assoc_drupal_user', isset($attendee['profile']['email']) ? $this->getUserIdByEmail($attendee['profile']['email']) : '');
          $entity->set('eventbrite_event', $this->getEventByEventbriteId($attendee['event_id']));
          $entity->set('ticket_cancelled', $attendee['cancelled']);
          $entity->set('attendee_status', $attendee['status']);
          $entity->save();
        }
        else {
          // Create the attendee if one does not yet exist.
          $entity = EventbriteEventsAttendee::create([
            'id' => $attendee['id'],
            'eventbrite_id' => $attendee['id'],
            'assoc_drupal_user' => isset($attendee['profile']['email']) ? $this->getUserIdByEmail($attendee['profile']['email']) : '',
            'eventbrite_event' => $this->getEventByEventbriteId($attendee['event_id']),
            'name' => $attendee['profile']['name'],
            'email' => isset($attendee['profile']['email']) ? $attendee['profile']['email'] : '',
            'job_title' => isset($attendee['profile']['job_title']) ? $attendee['profile']['job_title'] : '',
            'company' => isset($attendee['profile']['company']) ? $attendee['profile']['company'] : '',
            'ticket_class_name' => $attendee['ticket_class_name'],
            'ticket_class_id' => $attendee['ticket_class_id'],
            'ticket_cancelled' => $attendee['cancelled'],
            'attendee_status' => $attendee['status'],
          ]);
          $entity->save();
        }

      }
    }
  }

  /**
   * Determine whether the event is still active.
   *
   * @param string $event_status
   *   Strings describing an event's status.
   *
   * @return bool
   *   Whether or not the event is acgive
   */
  protected function activeEvent($event_status) {
    $inactive_states = ['draft', 'ended', 'completed', 'canceled'];
    if (in_array($event_status, $inactive_states)) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Look up Drupal user ID by email.
   *
   * @param string $email
   *   An email address.
   *
   * @return int|bool
   *   The user ID, if it exists.
   */
  protected function getUserIdByEmail($email) {
    $uid = FALSE;

    $user = user_load_by_mail($email);

    if ($user) {
      $uid = $user->id();
    }

    return $uid;
  }

  /**
   * Look up Event by Eventbrite ID value.
   *
   * @param int $eventbrite_event_id
   *   The Eventbrite ID.
   *
   * @return bool|int
   *   The event ID, if it exists.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getEventByEventbriteId($eventbrite_event_id) {
    $entity_id = FALSE;

    $entities = $this->entityTypeManager->getStorage('node')->loadByProperties([
      'type' => 'eventbrite_event',
      'eventbrite_event_id' => $eventbrite_event_id,
    ]);

    if ($entities) {
      // For now, assume one Eventbrite Event node.
      $entity = reset($entities);
      $entity_id = $entity->id();
    }
    return $entity_id;
  }

}
