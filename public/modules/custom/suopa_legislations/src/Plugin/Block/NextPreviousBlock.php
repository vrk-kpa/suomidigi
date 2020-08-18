<?php

namespace Drupal\suopa_legislations\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\suopa_legislations\LegislationService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Provides a 'Next Previous' block.
 *
 * @Block(
 *   id = "next_previous_block",
 *   admin_label = @Translation("Legislation Next/Previous link"),
 *   category = @Translation("Blocks")
 * )
 */
class NextPreviousBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * The Query Factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

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

  /**
   * Cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

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
   * @param \Drupal\Core\Entity\Query\QueryFactory $queryFactory
   *   The Query Factory.
   * @param \Drupal\suopa_legislations\LegislationService $legislationService
   *   The Query Factory.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The Renderer.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The Cache backend.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, EntityTypeManagerInterface $entityTypeManager, QueryFactory $queryFactory, LegislationService $legislationService, Renderer $renderer, CacheBackendInterface $cache_backend) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entityTypeManager;
    $this->queryFactory = $queryFactory;
    $this->legislationService = $legislationService;
    $this->renderer = $renderer;
    $this->cacheBackend = $cache_backend;
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
      $container->get('entity.query'),
      $container->get('suopa_legislations.legislation'),
      $container->get('renderer'),
      $container->get('cache.default')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof NodeInterface) {
      $current_nid = $node->id();

      $prev = $this->generatePrevious($current_nid);
      if (!empty($prev)) {
        $build['previous'] = ['#markup' => $prev];
      }

      $next = $this->generateNext($current_nid);
      if (!empty($next)) {
        $build['next'] = ['#markup' => $next];
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

  /**
   * Lookup the previous node.
   *
   * @param string $current_nid
   *   Show current page node id.
   *
   * @return array
   *   A render array for a previous node.
   */
  private function generatePrevious($current_nid) {
    return $this->generateNextPrevious($current_nid, 'prev');
  }

  /**
   * Lookup the next node.
   *
   * @param string $current_nid
   *   Show current page node id.
   *
   * @return array
   *   A render array for a next node.
   */
  private function generateNext($current_nid) {
    return $this->generateNextPrevious($current_nid, 'next');
  }

  /**
   * Render the next or previous node.
   *
   * @param string $current_nid
   *   Get current page node id.
   * @param string $direction
   *   Default value is "next" and other value come from
   *   generatePrevious() and generatePrevious().
   *
   * @return \Drupal\Component\Render\MarkupInterface|mixed|null
   *   Returns rendered buttons.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Throws exception if plugin definition is invalid.
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Throws exception if plugin is not found.
   */
  private function generateNextPrevious($current_nid, $direction = self::DIRECTION__NEXT) {
    /** @var \Drupal\suopa_legislations\LegislationService $legislation_cards */
    $nids = $this->legislationService->getNextAndPreviousCards($current_nid);
    $index = FALSE;

    // Get previous and next nodes.
    if (in_array($current_nid, $nids)) {
      foreach ($nids as $key => $id) {
        if ($id === $current_nid) {
          end($nids);
          $end = key($nids);
          if ($key !== $end) {
            $nid = $nids[$key + 1];
          }

          if ($direction === 'prev') {
            if ($key === 0) {
              $nid = $this->legislationService->getLegislationCollectionTerm($current_nid);
              $index = TRUE;
            }
            else {
              $nid = $nids[$key - 1];
            }
          }
          break;
        }
      }

      $display_text = $this->t('Next');

      if ($direction === 'prev') {
        $display_text = $this->t('Previous');
      }

      if (!empty($nid)) {
        $node = $this->entityTypeManager->getStorage('node')->load($nid);
        $currentLanguage = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $label = ($index)
          ? $this->t('Summary')
          : $node->hasTranslation($currentLanguage)
            ? $node->getTranslation($currentLanguage)->getTitle()
            : $node->label();

        $url = Url::fromRoute('entity.node.canonical', ['node' => $nid], []);
        $variables = [
          '#theme' => 'suopa_legislations_prev_next',
          '#direction' => $direction,
          '#label' => $label,
          '#url' => $url,
          '#display_text' => $display_text,
        ];

        return $this->renderer->renderPlain($variables);
      }
    }
    return NULL;
  }

}
