<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\Plugin\Derivative;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides local task definitions for all social media bundles.
 */
class SocialMediaLocalTask extends DeriverBase implements ContainerDeriverInterface {
  use StringTranslationTrait;

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Creates an FieldUiLocalTask object.
   *
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The translation manager.
   */
  public function __construct(RouteProviderInterface $route_provider, EntityManagerInterface $entity_manager, TranslationInterface $string_translation) {
    $this->routeProvider = $route_provider;
    $this->entityManager = $entity_manager;
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('router.route_provider'),
      $container->get('entity.manager'),
      $container->get('string_translation')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];
    $entity_type_id = 'social_media_post';

    foreach ($this->entityManager->getBundleInfo($entity_type_id) as $bundle_id => $bundle_info) {
      // 'Settings' tab.
      $this->derivatives["settings_overview_{$entity_type_id}_{$bundle_id}"] = [
        'route_name' => "entity.social_media_post.settings.{$bundle_id}",
        'weight' => 1,
        'title' => $this->t('Settings'),
        'base_route' => 'entity.social_media_post.settings.' . $bundle_id,
      ];

      // List tab.
      $this->derivatives["display_overview_{$entity_type_id}_{$bundle_id}"] = [
        'route_name' => "entity.entity_view_display.$entity_type_id.$bundle_id.default",
        'weight' => 2,
        'title' => $this->t('List'),
        'base_route' => 'entity.social_media_post.settings.' . $bundle_id,
      ];

      // 'Manage display' tab.
      $this->derivatives["display_overview_{$entity_type_id}_{$bundle_id}"] = [
        'route_name' => "entity.entity_view_display.$entity_type_id.$bundle_id.default",
        'weight' => 3,
        'title' => $this->t('Manage display'),
        'base_route' => 'entity.social_media_post.settings.' . $bundle_id,
      ];

      $this->derivatives["field_form_display_default_{$entity_type_id}_{$bundle_id}"] = [
        'title' => 'Default',
        'base_route' => 'entity.social_media_post.settings.' . $bundle_id,
        'route_name' => "entity.entity_form_display.$entity_type_id.default",
        'parent_id' => "field_ui.fields:form_display_overview_$entity_type_id",
        'weight' => -1,
      ];
      $this->derivatives["field_display_default_{$entity_type_id}_{$bundle_id}"] = [
        'title' => 'Default',
        'base_route' => 'entity.social_media_post.settings.' . $bundle_id,
        'route_name' => "entity.entity_view_display.$entity_type_id.default",
        'parent_id' => "field_ui.fields:display_overview_$entity_type_id",
        'weight' => -1,
      ];

      // One local task for each form mode.
      $weight = 0;
      foreach ($this->entityManager->getFormModes($entity_type_id) as $form_mode => $form_mode_info) {
        $this->derivatives['field_form_display_' . $form_mode . '_' . $entity_type_id . '_' . $bundle_id] = [
          'title' => $form_mode_info['label'],
          'base_route' => 'entity.social_media_post.settings.' . $bundle_id,
          'route_name' => "entity.entity_form_display.$entity_type_id.form_mode",
          'route_parameters' => [
            'form_mode_name' => $form_mode,
          ],
          'parent_id' => "field_ui.fields:form_display_overview_$entity_type_id",
          'weight' => $weight++,
          'cache_tags' => $this->entityManager->getDefinition('entity_form_display')
            ->getListCacheTags(),
        ];
      }

      // One local task for each view mode.
      $weight = 0;
      foreach ($this->entityManager->getViewModes($entity_type_id) as $view_mode => $form_mode_info) {
        $this->derivatives['field_display_' . $view_mode . '_' . $entity_type_id . '_' . $bundle_id] = [
          'title' => $form_mode_info['label'],
          'base_route' => 'entity.social_media_post.settings.' . $bundle_id,
          'route_name' => "entity.entity_view_display.$entity_type_id.view_mode",
          'route_parameters' => [
            'view_mode_name' => $view_mode,
          ],
          'parent_id' => "field_ui.fields:display_overview_$entity_type_id",
          'weight' => $weight++,
          'cache_tags' => $this->entityManager->getDefinition('entity_view_display')
            ->getListCacheTags(),
        ];
      }
    }

    foreach ($this->derivatives as &$entry) {
      $entry += $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
