<?php

namespace Drupal\eventbrite_events\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Eventbrite attendee entities.
 *
 * @ingroup eventbrite_events
 */
interface EventbriteEventsAttendeeInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Eventbrite attendee name.
   *
   * @return string
   *   Name of the Eventbrite attendee.
   */
  public function getName();

  /**
   * Sets the Eventbrite attendee name.
   *
   * @param string $name
   *   The Eventbrite attendee name.
   *
   * @return \Drupal\eventbrite_events\Entity\EventbriteEventsAttendeeInterface
   *   The called Eventbrite attendee entity.
   */
  public function setName($name);

  /**
   * Gets the Eventbrite attendee creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Eventbrite attendee.
   */
  public function getCreatedTime();

  /**
   * Sets the Eventbrite attendee creation timestamp.
   *
   * @param int $timestamp
   *   The Eventbrite attendee creation timestamp.
   *
   * @return \Drupal\eventbrite_events\Entity\EventbriteEventsAttendeeInterface
   *   The called Eventbrite attendee entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Eventbrite attendee published status indicator.
   *
   * Unpublished Eventbrite attendee are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Eventbrite attendee is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Eventbrite attendee.
   *
   * @param bool $published
   *   TRUE to set this Eventbrite attendee to published, FALSE to set it to
   *   unpublished.
   *
   * @return \Drupal\eventbrite_events\Entity\EventbriteEventsAttendeeInterface
   *   The called Eventbrite attendee entity.
   */
  public function setPublished($published);

}
