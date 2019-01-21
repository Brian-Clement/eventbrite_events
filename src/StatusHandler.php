<?php

namespace Drupal\eventbrite_events;
use Drupal\eventbrite_events\ApiInterface;

/**
 * Class StatusHandler.
 */
class StatusHandler {

  /**
   * Drupal\eventbrite_events\ApiInterface definition.
   *
   * @var \Drupal\eventbrite_events\ApiInterface
   */
  protected $eventbriteEventsApi;
  /**
   * Constructs a new StatusHandler object.
   */
  public function __construct(ApiInterface $eventbrite_events_api) {
    $this->eventbriteEventsApi = $eventbrite_events_api;
  }

}
