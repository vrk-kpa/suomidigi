<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the social media post entity type.
 */
class SocialMediaPostAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function access(EntityInterface $entity, $operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = AccessResult::forbidden()->cachePerPermissions();

    if (!$account) {
      $account = \Drupal::currentUser();
    }

    switch ($operation) {
      case 'view':
        if ($account->hasPermission('view social media posts')) {
          $result = AccessResult::allowed()->cachePerPermissions();
          return $return_as_object ? $result : $result->isAllowed();
        }
        break;

      case 'edit':
      case 'delete':
        if ($account->hasPermission('administer social media settings')) {
          $result = AccessResult::allowed()->cachePerPermissions();
          return $return_as_object ? $result : $result->isAllowed();
        }
        break;
    }

    return $return_as_object ? $result : $result->isAllowed();
  }

  /**
   * {@inheritdoc}
   */
  public function createAccess($entity_bundle = NULL, AccountInterface $account = NULL, array $context = [], $return_as_object = FALSE) {
    return $return_as_object ? AccessResult::forbidden() : FALSE;
  }

}
