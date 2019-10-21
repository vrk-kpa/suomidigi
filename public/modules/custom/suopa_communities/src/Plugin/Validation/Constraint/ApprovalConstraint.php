<?php

namespace Drupal\suopa_communities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Prevent article creation if limit is reached.
 *
 * @Constraint(
 *   id = "ApprovalConstraint",
 *   label = @Translation("Prevent entity save action if the entity is set as published but is not approved.", context = "Validation"),
 *   type = "entity"
 * )
 */
class ApprovalConstraint extends Constraint {

  /**
   * Message shown when trying a published entity without approval.
   *
   * @var string
   */
  public $message = 'Save failed: Entity has to be approved in order to be published.';

}
