<?php

namespace Drupal\eventbrite_events;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Eventbrite attendee entity.
 *
 * @see \Drupal\eventbrite_events\Entity\EventbriteEventsAttendee.
 */
class EventbriteEventsAttendeeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\eventbrite_events\Entity\EventbriteEventsAttendeeInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished eventbrite attendee entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published eventbrite attendee entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit eventbrite attendee entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete eventbrite attendee entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add eventbrite attendee entities');
  }

}
