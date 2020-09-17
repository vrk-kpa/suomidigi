<?php

namespace Drupal\suopa_content\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Refresh view when flag link gets updated.
 */
class UpdateCustomViewCommand implements CommandInterface {

  /**
   * The view to be flashed under the link.
   *
   * @var string
   */
  protected $view;

  /**
   * Construct a view refresher.
   *
   * @param string $view
   *   The view to be refreshed.
   */
  public function __construct($view) {
    $this->view = $view;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'updateCustomView',
      'view' => $this->view,
    ];
  }

}
