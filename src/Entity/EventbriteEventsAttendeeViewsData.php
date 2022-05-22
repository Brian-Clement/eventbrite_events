<?php

namespace Drupal\eventbrite_events\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Eventbrite attendee entities.
 */
class EventbriteEventsAttendeeViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
