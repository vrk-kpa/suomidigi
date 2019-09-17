<?php

namespace Drupal\suopa_editorial\Plugin\Action;

/**
 * Action description.
 *
 * @Action(
 *   id = "suopa_editorial_approve_and_publish_action",
 *   label = @Translation("Approve and publish"),
 *   type = ""
 * )
 */
class ApproveAndPublishAction extends ApproveBaseAction {

  /**
   * {@inheritdoc}
   */
  public function execCommand($entity = NULL) {
    $entity->set('field_approved', TRUE);
    $entity->setPublished(TRUE);
    $entity->save();
  }

}
