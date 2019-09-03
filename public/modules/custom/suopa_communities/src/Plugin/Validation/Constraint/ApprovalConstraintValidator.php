<?php

namespace Drupal\suopa_communities\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for ApprovalConstraint.
 */
class ApprovalConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
    if (!isset($entity)) {
      return;
    }

    // The entity has to have the status (published) field and the
    // approved field.
    if (!$entity->hasField('status') || !$entity->hasField('field_approved')) {
      return;
    }

    // Entity has to be approved in order to be published.
    if ($entity->get('status')->value == TRUE &&
      $entity->get('field_approved')->value == FALSE) {
      $this->context->addViolation($constraint->message);
    }
  }

}
