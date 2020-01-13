<?php

namespace Drupal\suopa_dream_broker\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class DreamBrokerOembedController.
 */
class DreamBrokerOembedController extends ControllerBase {

  /**
   * Dream Broker Embeddable URL.
   *
   * @var string
   */
  protected $dreamBrokerUrl;

  /**
   * Dream Broker Channel ID.
   *
   * @var string
   */
  protected $channelId;

  /**
   * Dream Broker Video ID.
   *
   * @var string
   */
  protected $videoId;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;


  /**
   * The current request from request stack.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * Instantiates a new instance of this class.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   *
   * @return \Drupal\Core\Controller\ControllerBase|\Drupal\suopa_dream_broker\Controller\DreamBrokerOembedController
   *   Returns the static container.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }

  /**
   * Constructs a DreamBrokerOembedController.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
    $this->currentRequest = $this->requestStack->getCurrentRequest();
  }

  /**
   * Callback for the Oembed JSON.
   */
  public function oembed() {
    $this->dreamBrokerUrl = $this->currentRequest->query->get('url');

    if (!empty($this->dreamBrokerUrl) && $this->getIdFromInput()) {
      list($this->channelId, $this->videoId) = $this->getIdFromInput();
      return new JsonResponse($this->getResults());
    }

    return new JsonResponse([]);
  }

  /**
   * A helper function for returning results.
   */
  private function getResults() {
    $url = 'https://dreambroker.com/channel/' . $this->channelId . '/iframe/' . $this->videoId;
    return [
      'html' => '<iframe width="480" height="270" src="' . $url . '" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen</iframe>',
      'height' => 270,
      'type' => 'video',
      'thumbnail_width' => 480,
      'provider_url' => 'https://www.dreambroker.com/',
      'version' => '1.0',
      'thumbnail_height' => 360,
      'width' => 480,
      'thumbnail_url' => $this->getRemoteThumbnailUrl(),
      'provider_name' => 'Dream Broker',
      'title' => 'Dream Broker Video',
    ];
  }

  /**
   * {@inheritdoc}
   */
  private function getIdFromInput() {
    $matches = [];
    preg_match('/(?:channel\/([a-z0-9]{8}))\/([a-z0-9]{8})/', $this->dreamBrokerUrl, $matches);

    if ($matches && !empty($matches[1]) && !empty($matches[2])) {
      return [$matches[1], $matches[2]];
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  private function getRemoteThumbnailUrl() {
    $url = "https://dreambroker.com/channel/$this->channelId/$this->videoId/get/poster.png";
    return $url;
  }

}
