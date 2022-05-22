<?php

namespace Drupal\eventbrite_events;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * The API service.
 */
class Api implements ApiInterface {
  use StringTranslationTrait;

  /**
   * GuzzleHttp\ClientInterface definition.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The log service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $logger;

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
  protected $baseUri;

  /**
   * Constructs a new Api object.
   */
  public function __construct(ClientInterface $http_client, ConfigFactory $config_factory, MessengerInterface $messenger, LoggerChannelFactory $logger) {
    $this->httpClient = $http_client;
    $this->config = $config_factory->get('eventbrite_events.eventbritesettings');
    $this->messenger = $messenger;
    $this->logger = $logger;
    $this->token = $this->config->get('eventbrite_oauth_token');
    $this->baseUri = 'https://www.eventbriteapi.com/v3/';
  }

  /**
   * {@inheritdoc}
   */
  public function connect($method, $endpoint, $query, $body, $page = 1) {
    try {
      $response = $this->httpClient->{$method}(
        $this->baseUri . $endpoint,
        $this->buildOptions($query, $body)
      );
    }
    catch (RequestException $exception) {
      $msg = $this->t('Eventbrite "%error"', ['%error' => $exception->getMessage()]);
      $this->messenger->addMessage($msg, $this->messenger::TYPE_ERROR, TRUE);
      $this->logger->get('eventbrite_events.api')->error('Eventbrite "%error"', ['%error' => $exception->getMessage()]);
      return FALSE;
    }

    $pages = [];

    $results = json_decode($response->getBody(), TRUE);

    $pages[$page] = $results;

    if (isset($results['pagination']) && $results['pagination']['has_more_items'] && $results['pagination']['page_number'] < $results['pagination']['page_count']) {
      $query = [
        'continuation' => $results['pagination']['continuation'],
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
