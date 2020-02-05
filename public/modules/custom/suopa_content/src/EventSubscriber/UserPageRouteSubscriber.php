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

    if ($route = $collection->get('flag.action_link_unflag')) {
      $route->setDefault(
        '_controller',
        'Drupal\suopa_content\EventSubscriber\SuopaContentActionLinkController::unflag'
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
    $title = t('My profile');

    if (
      !empty($viewed_user = \Drupal::routeMatch()->getParameter('user')) &&
      $viewed_user->hasField('field_first_name') &&
      $viewed_user->hasField('field_last_name')
    ) {
      $first_name = $viewed_user->field_first_name->value;
      $last_name = $viewed_user->field_last_name->value;
      $user = \Drupal::currentUser();

      if ($user->id() !== $viewed_user->id()) {
        $title = (!empty($first_name) && !empty($last_name))
          ? $first_name . ' ' . $last_name
          : explode('@', $viewed_user->getAccountName())[0];
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $title,
    ];
  }

}
