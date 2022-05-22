<?php

namespace Drupal\eventbrite_events;

/**
 * The API Interface.
 */
interface ApiInterface {

  /**
   * Utilizes Drupal's httpClient to connect to the Eventbrite API.
   *
   * @param string $method
   *   get, post, patch, delete, etc. See Guzzle documentation.
   * @param string $endpoint
   *   The Eventbrite API endpoint (ex. users/me/owned_events)
   * @param array $query
   *   Query string parameters the endpoint allows (ex. ['per_page' => 50].
   * @param array $body
   *   (converted to JSON)
   *   Utilized for some endpoints.
   *
   * @return object
   *   \GuzzleHttp\Psr7\Response body
   */
  public function connect($method, $endpoint, array $query, array $body);

}
