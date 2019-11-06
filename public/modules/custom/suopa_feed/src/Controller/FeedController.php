<?php

namespace Drupal\suopa_feed\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\suopa_feed\Feed\GuzzleClient;
use Zend\Feed\Reader\Reader;

/**
 * Class FeedController.
 */
class FeedController extends ControllerBase {

  /**
   * Load.
   *
   * @param $id
   *
   * @return AjaxResponse
   *   Load feed paragraph data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function load($id) {
    $response = new AjaxResponse();
    $paragraph = \Drupal::entityTypeManager()->getStorage('paragraph')->load($id);

    if ($paragraph->getType() != 'feed') {
      return $response;
    }

    $renderArray = $this->renderParagraph($paragraph);

    $response->addCommand(new ReplaceCommand('#feed-' . $id, "<div>THIS IS REPLACED</div>"));
    return $response;
  }

  /**
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph to render.
   *
   * @return array
   *   Render array of the paragraph.
   */
  protected function renderParagraph($paragraph) {
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
      foreach($feed as $item) {
        // Limit the number of items if limit set.
        if ($limit && $counter == $limit) {
          break;
        }
        $counter++;

        $renderItem = [];
        $renderItem['title'] = $item->getTitle();
        $renderItem['link'] = $item->getLink();
        $renderItem['created'] = $item->getDateCreated();
        $renderItem['authors'] = (array) $item->getAuthors();

        $renderItems[] = $renderItem;
      }
    }

    return $renderItems;
  }
}
