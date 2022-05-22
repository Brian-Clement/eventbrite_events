<?php

/**
 * @file
 * Contains eventbrite_events_attendee.page.inc.
 *
 * Page callback for Eventbrite attendee entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Eventbrite attendee templates.
 *
 * Default template: eventbrite_events_attendee.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_eventbrite_events_attendee(array &$variables) {
  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
