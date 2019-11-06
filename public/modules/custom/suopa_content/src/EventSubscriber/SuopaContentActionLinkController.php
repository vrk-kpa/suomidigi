<?php

namespace Drupal\suopa_content\EventSubscriber;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityInterface;
use Drupal\flag\Ajax\ActionLinkFlashCommand;
use Drupal\suopa_content\Ajax\UpdateCustomViewCommand;
use Drupal\flag\FlagInterface;
use Drupal\flag\Controller\ActionLinkController;
use Drupal\Component\Utility\Html;

/**
 * Controller responses to flag and unflag action links.
 *
 * The response is a set of AJAX commands to update the
 * link in the page.
 */
class SuopaContentActionLinkController extends ActionLinkController {

  /**
   * Performs a flagging when called via a route.
   *
   * @param \Drupal\flag\FlagInterface $flag
   *   The flag entity.
   * @param int $entity_id
   *   The flaggable entity ID.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse|null
   *   The response object, only if successful.
   *
   * @see \Drupal\flag\Plugin\Reload
   */
  public function flag(FlagInterface $flag, $entity_id) {
    /* @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = $this->flagService->getFlaggableById($flag, $entity_id);

    try {
      $this->flagService->flag($flag, $entity);
    }
    catch (\LogicException $e) {
      // Fail silently so we return to the entity, which will show an updated
      // link for the existing state of the flag.
    }

    return $this->generateResponse($flag, $entity, $flag->getMessage('flag'));
  }

  /**
   * Performs a unflagging when called via a route.
   *
   * @param \Drupal\flag\FlagInterface $flag
   *   The flag entity.
   * @param int $entity_id
   *   The flaggable entity ID.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse|null
   *   The response object, only if successful.
   *
   * @see \Drupal\flag\Plugin\Reload
   */
  public function unflag(FlagInterface $flag, $entity_id) {
    /* @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = $this->flagService->getFlaggableById($flag, $entity_id);

    try {
      $this->flagService->unflag($flag, $entity);
    }
    catch (\LogicException $e) {
      // Fail silently so we return to the entity, which will show an updated
      // link for the existing state of the flag.
    }

    return $this->generateResponse($flag, $entity, $flag->getMessage('unflag'));
  }

  /**
   * Generates a response after the flag has been updated.
   *
   * @param \Drupal\flag\FlagInterface $flag
   *   The flag entity.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity object.
   * @param string $message
   *   (optional) The message to flash.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The response object.
   */
  private function generateResponse(FlagInterface $flag, EntityInterface $entity, $message) {
    // Create a new AJAX response.
    $response = new AjaxResponse();

    // Get the link type plugin.
    $link_type = $flag->getLinkTypePlugin();

    // Generate the link render array.
    $link = $link_type->getAsFlagLink($flag, $entity);

    // Generate a CSS selector to use in a JQuery Replace command.
    $selector = '.js-flag-' . Html::cleanCssIdentifier($flag->id()) . '-' . $entity->id();

    // Create a new JQuery Replace command to update the link display.
    $replace = new ReplaceCommand($selector, $this->renderer->renderPlain($link));
    $response->addCommand($replace);

    // Push a message pulsing command onto the stack.
    $pulse = new ActionLinkFlashCommand($selector, $message);
    $response->addCommand($pulse);

    // Refresh appropriate view. TODO: Retrieve the view id.
    $view = 'profile_my_bookmarks';
    $refreshView = new UpdateCustomViewCommand($view);
    $response->addCommand($refreshView);

    return $response;
  }

}
