<?php

namespace Drupal\suopa_feed\Feed;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\Feed\Reader\Http\Psr7ResponseDecorator;


/**
 * Class GuzzleClient
 */
class GuzzleClient implements FeedReaderHttpClientInterface {

  /**
   * @var GuzzleClientInterface
   */
  private $client;

  /**
   * GuzzleClient constructor.
   *
   * @param GuzzleClientInterface|null $client
   */
  public function __construct(GuzzleClientInterface $client = NULL) {
    $this->client = $client ?: new Client();
  }

  /**
   * {@inheritdoc}
   */
  public function get($uri) {
    $response = $this->client->request('GET', $uri);
    return new Psr7ResponseDecorator($response);
  }
}
