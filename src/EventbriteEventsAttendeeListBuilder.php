<?php

namespace Drupal\eventbrite_events;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Eventbrite attendee entities.
 *
 * @ingroup eventbrite_events
 */
class EventbriteEventsAttendeeListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Attendee ID');
    $header['name'] = $this->t('Name');
    $header['event'] = $this->t('Event');
    $header['ticket'] = $this->t('Ticket type');
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    // Get the parent event entity name.
    /** @var \Drupal\node\Entity\Node $entity */
    $nid = $entity->get('eventbrite_event')->getString();
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $parent_event = $node_storage->load($nid);

    /** @var \Drupal\eventbrite_events\Entity\EventbriteEventsAttendee $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.eventbrite_events_attendee.edit_form',
      ['eventbrite_events_attendee' => $entity->id()]
    );
    $row['event'] = Link::createFromRoute(
      $parent_event->title->value,
      'entity.node.canonical',
      ['node' => $entity->get('eventbrite_event')->getString()]
    );
    $row['ticket'] = $entity->get('ticket_class_name')->getString();
    $row['status'] = $entity->get('attendee_status')->getString();
    return $row + parent::buildRow($entity);
  }

}
