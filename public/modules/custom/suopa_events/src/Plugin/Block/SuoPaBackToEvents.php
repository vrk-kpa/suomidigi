<?php

namespace Drupal\suopa_events\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides a 'SuoPaBackToEvents' block.
 *
 * @Block(
 *  id = "suopa_back_to_events",
 *  admin_label = @Translation("Back to events"),
 * )
 */
class SuoPaBackToEvents extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The configFactory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('config.factory'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $nid = $this->configFactory->get('suopa_events.settings')->get('events_nid');
    $label = $this->configFactory->get('suopa_events.settings')->get('events_label');

    $options = ['absolute' => TRUE];
    $url = Url::fromRoute('entity.node.canonical', ['node' => $nid], $options);

    $build['#url'] = $url->toString();
    $build['#link_title'] = $label;

    $build['#theme'] = 'suopa_back_to_events';
    return $build;
  }

}
