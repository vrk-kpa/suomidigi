<?php

namespace Drupal\suopa_feed\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Render\Markup;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\suopa_feed\Feed\GuzzleClient;
use Zend\Feed\Reader\Reader;

/**
 * Class FeedController.
 */
class FeedController extends ControllerBase {

  /**
   * Load.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The feed paragraph to render (and to fetch feeds for).
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Load feed paragraph data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function load(Paragraph $paragraph) {
    $response = new AjaxResponse();

    if ($paragraph->getType() != 'feed') {
      return $response;
    }

    $renderArray = $this->renderParagraph($paragraph);

    $response->addCommand(new ReplaceCommand('.suopa-feed-' . $paragraph->id(), $renderArray));
    return $response;
  }

  /**
   * Render paragraph before sending it back via AJAX.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph to render.
   *
   * @return array
   *   Render array of the paragraph.
   */
  protected function renderParagraph(Paragraph $paragraph) {
    $limit = $paragraph->get('field_limit')->value;
    $keyword = $paragraph->get('field_keyword')->value;

    // Get feed types. Return if none found.
    $feedTypes = $paragraph->get('field_feed_type')->referencedEntities();
    if (!count($feedTypes)) {
      return [];
    }
    $feedType = reset($feedTypes);

    // Get feed sources. Return if none found.
    $feedSources = $feedType->get('field_feed_source')->getValue();
    if (!count($feedSources)) {
      return [];
    }

    Reader::setHttpClient(new GuzzleClient());
    $renderArray = [];

    // Fetch and format the data from the sources.
    foreach ($feedSources as $source) {
      $renderedSource = [
        '#theme' => 'suopa_feed_source',
        '#title' => $source['name'],
      ];

      // Fetch feed items and place them to the rendered source.
      $formattedUrl = sprintf($source['url'], $keyword);
      $renderedItems = $this->renderFeed($formattedUrl, $limit);
      $renderedSource['#items'] = $renderedItems;

      $renderArray[] = $renderedSource;
    }

    return $renderArray;
  }

  /**
   * Render a feed based on the given parameters.
   *
   * @param string $url
   *   The URL of the feed.
   * @param int $limit
   *   Number of items to fetch.
   *
   * @return array
   *   A render array containing the items.
   */
  protected function renderFeed($url, $limit) {
    $renderedItemsArray = [];

    $feed = Reader::import($url);

    $counter = 0;
    foreach ($feed as $item) {
      // Limit the number of items if limit set.
      if ($limit && $counter == $limit) {
        break;
      }
      $counter++;

      $renderItem = [
        '#theme' => 'suopa_feed_item',
      ];
      $renderItem['#title'] = Markup::create($item->getTitle());

      // Sanitize the link with Drupal's Url class.
      try {
        $link = Url::fromUri($item->getLink())->toString();
      }
      catch (\Exception $e) {
        $link = '';
      }
      $renderItem['#link'] = $link;
      $renderItem['#published'] = $item->getDateCreated()->format("Y-m-d H:i:s");
      $renderItem['#authors'] = array_map(function ($i) {
        return $i['name'];
      }, (array) $item->getAuthors());
      $renderItem['#authors'] = Markup::create(implode(" & ", $renderItem['#authors']));

      $renderedItemsArray[] = $renderItem;
    }

    return $renderedItemsArray;
  }

}
