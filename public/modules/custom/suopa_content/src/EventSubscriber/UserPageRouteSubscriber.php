<?php

namespace Drupal\suopa_content\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class UserPageRouteSubscriber.
 */
class UserPageRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    // Retrieve the user edit and view form route.
    if ($route = $collection->get('entity.user.canonical')) {
      $route->setDefault(
        '_title_callback',
        'Drupal\suopa_content\EventSubscriber\UserPageRouteSubscriber::userTitle'
      );
    }

    if ($route = $collection->get('entity.user.edit_form')) {
      $route->setDefault(
        '_title_callback',
        'Drupal\suopa_content\EventSubscriber\UserPageRouteSubscriber::userTitle'
      );
    }
  }

  /**
   * Get user title.
   *
   * @return array
   *   Returns title as markup.
   */
  public function userTitle() {
    return [
      '#type' => 'markup',
      '#markup' => t('My profile'),
    ];
  }

}
