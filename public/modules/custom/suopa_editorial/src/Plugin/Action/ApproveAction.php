<?php

namespace Drupal\suopa_editorial\Plugin\Action;

/**
 * Action description.
 *
 * @Action(
 *   id = "suopa_editorial_approve_action",
 *   label = @Translation("Approve"),
 *   type = ""
 * )
 */
class ApproveAction extends ApproveBaseAction {

  /**
   * {@inheritdoc}
   */
  public function execCommand($entity = NULL) {
    $entity->set('field_approved', TRUE);
    $entity->save();
  }

}
