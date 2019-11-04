<?php

namespace Drupal\suopa_editorial\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\select2\Plugin\Field\FieldWidget\Select2EntityReferenceWidget;
use Drupal\user\EntityOwnerInterface;
use Drupal\views\ViewExecutableFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'select2' widget.
 *
 * @FieldWidget(
 *   id = "select2_entity_reference_views",
 *   label = @Translation("Select2 - Views reference"),
 *   field_types = {
 *     "entity_reference",
 *   },
 *   multiple_values = TRUE
 * )
 */
class Select2EntityReferenceViewsWidget extends Select2EntityReferenceWidget implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The exec factory.
   *
   * @var \Drupal\views\ViewExecutableFactory
   */
  protected $viewFactory;

  /**
   * The loader.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $viewLoader;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('entity_type.manager')->getStorage('view'),
      $container->get('views.executable'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager, EntityStorageInterface $view_loader, ViewExecutableFactory $view_factory, RendererInterface $renderer) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings, $entity_type_manager);

    $this->entityTypeManager = $entity_type_manager;
    $this->viewLoader = $view_loader;
    $this->viewFactory = $view_factory;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['#target_type'] = $this->getFieldSetting('target_type');
    $element['#selection_handler'] = $this->getFieldSetting('handler');
    $element['#selection_settings'] = $this->getSelectionSettings();
    $element['#autocomplete'] = $this->getSetting('autocomplete');

    if ($this->getSelectionHandlerSetting('auto_create') && ($bundle = $this->getAutocreateBundle())) {
      $entity = $items->getEntity();
      $element['#autocreate'] = [
        'bundle' => $bundle,
        'uid' => ($entity instanceof EntityOwnerInterface) ? $entity->getOwnerId() : \Drupal::currentUser()->id(),
      ];
    }

    if ($this->getFieldSettings()['handler'] == 'views') {
      $options = $this->getOptions($items->getEntity());
      $view_name = $this->getFieldSettings()['handler_settings']['view']['view_name'];
      $view = $this->viewFactory->get($this->viewLoader->load($view_name));
      $view->setArguments($this->getFieldSettings()['handler_settings']['view']['arguments']);
      $view->execute($this->getFieldSettings()['handler_settings']['view']['display_name']);
      $filter_options = [];
      foreach ($view->result as $row) {
        $row_output = $view->style_plugin->view->rowPlugin->render($row);
        $filter_options[$row->_entity->id()] = $this->renderer->renderPlain($row_output);

        // Handle User entity reference view results separately. If debugging is
        // enabled, the results won't be handled correctly.
        if (
          property_exists($row_output['#row'], 'user__field_last_name_field_last_name_value') &&
          is_null($row_output['#row']->user__field_last_name_field_last_name_value)
        ) {
          unset($filter_options[$row->_entity->id()]);
        }
      }
      $options = $filter_options;
      // Add an empty option if the widget needs one.
      if ($empty_label = $this->getEmptyLabel()) {
        $options = ['_none' => $empty_label] + $options;
      }

      $element['#options'] = $options;
    }

    if ($element['#autocomplete'] && $element['#multiple']) {
      $entity_definition = $this->entityTypeManager->getDefinition($element['#target_type']);
      $message = $this->t("Drag to re-order @entity_types.", ['@entity_types' => $entity_definition->getPluralLabel()]);
      if (!empty($element['#description'])) {
        $element['#description'] = [
          '#theme' => 'item_list',
          '#items' => [$element['#description'], $message],
        ];
      }
      else {
        $element['#description'] = $message;
      }
    }

    return $element;
  }

}
