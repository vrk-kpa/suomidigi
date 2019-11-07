<?php

namespace Drupal\suopa_feed\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Element\Url;
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

    $response->addCommand(new AppendCommand('#feed-' . $paragraph->id(), $renderArray));
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
    $renderItems = [];

    // Fetch and format the data from the sources.
    foreach ($feedSources as $source) {
      $formattedUrl = sprintf($source['url'], $keyword);
      $feed = Reader::import($formattedUrl);

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
        $renderItem['#link'] = Url:: $item->getLink();
        $renderItem['#published'] = $item->getDateCreated()->format("Y-m-d H:i:s");
        $renderItem['#authors'] = array_map(function ($i) {
          return $i['name'];
        }, (array) $item->getAuthors());
        $renderItem['#authors'] = Markup::create(implode(" & ", $renderItem['#authors']));

        $renderItems[] = $renderItem;
      }
    }

    return $renderItems;
  }

}
