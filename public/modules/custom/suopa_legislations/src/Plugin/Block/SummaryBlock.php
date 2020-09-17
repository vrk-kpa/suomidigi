<?php

namespace Drupal\suopa_legislations\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\suopa_legislations\LegislationService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides a 'Summary' block for legislation cards..
 *
 * @Block(
 *   id = "legislation_summary_block",
 *   admin_label = @Translation("Legislation Summary"),
 *   category = @Translation("Blocks")
 * )
 */
class SummaryBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Legislation service.
   *
   * @var \Drupal\suopa_legislations\LegislationService
   */
  protected $legislationService;

  /**
   * The Renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  const DIRECTION__NEXT = 'next';

  /**
   * Creates a NextPreviousBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\suopa_legislations\LegislationService $legislationService
   *   The Legislation service.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The Renderer.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, EntityTypeManagerInterface $entityTypeManager, LegislationService $legislationService, Renderer $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entityTypeManager;
    $this->legislationService = $legislationService;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
      $container->get('suopa_legislations.legislation'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof NodeInterface) {
      $nid = $this->legislationService->getLegislationCollectionTerm($node->id());
      if (!empty($nid)) {
        $node = $this->entityTypeManager->getStorage('node')->load($nid);
        $url = Url::fromRoute('entity.node.canonical', ['node' => $nid], []);
        $currentLanguage = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $label = $node->hasTranslation($currentLanguage) ? $node->getTranslation($currentLanguage)->getTitle() : $node->label();
        $build = [
          '#theme' => 'suopa_legislations_summary',
          '#label' => $label,
          '#url' => $url,
        ];
      }
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $node = $this->routeMatch->getParameter('node');
    if (!empty($node)) {
      return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
    }
    else {
      return parent::getCacheTags();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

}
