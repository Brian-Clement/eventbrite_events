<?php

namespace Drupal\eventbrite_events;

use Drupal\eventbrite_events\Api;
use Drupal\node\Entity\Node;

/**
 * Class StatusHandler.
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
   * @return string
   */
  public function check($eventbrite_event_id) {
    // Get the status response for the given event ID
    $results = $this->eventbriteEventsApi->connect('get', "events/$eventbrite_event_id/", [], []);

    $result = reset($results);

    // Return Eventbrite's status code.
    return $result['status'];
  }

  /**
   * Update an Event Node.
   *
   * @param \Drupal\node\Entity\Node $event
   */
  public function update(Node $event) {
    // Call the API to check the event status
    $eventbrite_event_id = $event->get('eventbrite_event_id')->value;
    $status = $this->check($eventbrite_event_id);

    // Set the status and save the node
    $event->set('eventbrite_event_status', $status);
    $event->save();
  }

}
