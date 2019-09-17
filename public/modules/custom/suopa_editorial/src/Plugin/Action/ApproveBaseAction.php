<?php

namespace Drupal\suopa_editorial\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

abstract class ApproveBaseAction extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  protected abstract function execCommand($entity = NULL);

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if (!$entity) {
      return $this->t('Missing entity');
    }

    if (!$entity->hasField('field_approved')) {
      return $this->t('The entity does not have an approval field.');
    }

    $this->execCommand($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object->getEntityType()) {
      $access = $object->access('update', $account, TRUE)
        ->andIf($object->status->access('edit', $account, TRUE));
      return $return_as_object ? $access : $access->isAllowed();
    }

    // Other entity types may have different
    // access methods and properties.
    return TRUE;
  }

}
