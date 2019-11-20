<?php

namespace Drupal\suopa_group\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubsciber.
 *
 * @package Drupal\suopa_group\Routing
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('view.group_members.page_1')) {
      $route->addOptions(['_admin_route' => TRUE]);
    }
    if ($route = $collection->get('view.group_nodes.page_1')) {
      $route->addOptions(['_admin_route' => TRUE]);
    }
  }

}
