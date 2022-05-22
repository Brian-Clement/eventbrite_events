<?php

namespace Drupal\eventbrite_events;

use Drupal\node\Entity\Node;

/**
 * Handle statuses returned by the API.
 */
class StatusHandler {

  /**
   * Drupal\eventbrite_events\ApiInterface definition.
   *
   * @var \Drupal\eventbrite_events\Api
   */
  protected $eventbriteEventsApi;

  /**
   * Constructs a new StatusHandler object.
   */
  public function __construct(Api $eventbrite_events_api) {
    $this->eventbriteEventsApi = $eventbrite_events_api;
  }

  /**
   * Check the status of the event.
   *
   * @param int $eventbrite_event_id
   *   The Eventbrite ID.
   *
   * @return string
   *   The status code provided by Eventbrite.
   */
  public function check($eventbrite_event_id) {
    // Get the status response for the given event ID.
    $results = $this->eventbriteEventsApi->connect('get', "events/$eventbrite_event_id/", [], []);

    if ($results) {
      $result = reset($results);
      return $result['status'];
    }

    return;
  }

  /**
   * Update an Event Node.
   *
   * @param \Drupal\node\Entity\Node $event
   *   The Event node.
   */
  public function update(Node $event) {
    // Call the API to check the event status.
    $eventbrite_event_id = $event->get('eventbrite_event_id')->value;
    $status = $this->check($eventbrite_event_id);

    // Set the status and save the node.
    $event->set('eventbrite_event_status', $status);
    $event->save();
  }

}
