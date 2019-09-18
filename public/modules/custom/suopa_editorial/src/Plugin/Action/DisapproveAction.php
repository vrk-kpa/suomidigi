<?php

namespace Drupal\suopa_editorial\Plugin\Action;

/**
 * Action description.
 *
 * @Action(
 *   id = "suopa_editorial_disapprove_action",
 *   label = @Translation("Disapprove"),
 *   type = ""
 * )
 */
class DisapproveAction extends ApproveBaseAction {

  /**
   * {@inheritdoc}
   */
  public function execCommand($entity = NULL) {
    $entity->set('field_approved', FALSE);
    $entity->setPublished(FALSE);
    $entity->save();
  }

}
