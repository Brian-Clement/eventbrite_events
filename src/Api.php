<?php

namespace Drupal\eventbrite_events;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Class Api.
 */
class Api implements ApiInterface {

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory;
   */
  protected $config;

  /**
   * Eventbrite API Token.
   *
   * @var string
   */
  protected $token;

  /**
   * Eventbrite Base URI.
   *
   * @var string
   */
  protected $base_uri;

  /**
   * Constructs a new Api object.
   */
  public function __construct(ClientInterface $http_client, ConfigFactory $config_factory) {
    $this->httpClient = $http_client;
    $this->config = $config_factory->get('eventbrite_events.eventbritesettings');
    $this->token = $this->config->get('eventbrite_oauth_token');
    $this->base_uri = 'https://www.eventbriteapi.com/v3/';
  }

  /**
   * { @inheritdoc }
   */
  public function connect($method, $endpoint, $query, $body, $page = 1) {
    try {
      $response = $this->httpClient->{$method}(
        $this->base_uri . $endpoint,
        $this->buildOptions($query, $body)
      );
    }
    catch (RequestException $exception) {
      $msg = t('Eventbrite "%error"', ['%error' => $exception->getMessage()]);
      \Drupal::messenger()->addMessage($msg, MessengerInterface::TYPE_ERROR, TRUE);
      \Drupal::logger('eventbrite_events.api')->error('Eventbrite "%error"', ['%error' => $exception->getMessage()]);
      return FALSE;
    }

    $pages = [];

    $results = json_decode($response->getBody(), TRUE);

    $pages[$page] = $results;

    if (isset($results['pagination']) && $results['pagination']['has_more_items'] && $results['pagination']['page_number'] < $results['pagination']['page_count']) {
      $query = [
        'continuation' => $results['pagination']['continuation']
      ];

      $pages += $this->connect($method, $endpoint, $query, $body, $page + 1);
    }

    return $pages;
  }

  /**
   * Build options for the client.
   */
  private function buildOptions($query, $body) {
    $options = [];
    $options['headers'] = [
      'Authorization' => 'Bearer ' . $this->token,
    ];
    if ($body) {
      $options['body'] = $body;
    }
    if ($query) {
      $options['query'] = $query;
    }
    return $options;
  }

}
