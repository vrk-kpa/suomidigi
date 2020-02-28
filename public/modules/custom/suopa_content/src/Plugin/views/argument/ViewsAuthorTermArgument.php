<?php

namespace Drupal\suopa_content\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\NumericArgument;

/**
 * Defines a filter for authors.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("views_author_term_argument")
 */
class ViewsAuthorTermArgument extends NumericArgument {

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {
    $this->ensureMyTable();

    if (!empty($this->options['break_phrase'])) {
      $break = static::breakString($this->argument, FALSE);
      $this->value = $break->value;
      $this->operator = $break->operator;
    }
    else {
      $this->value = [$this->argument];
    }

    $placeholder = $this->placeholder();
    $null_check = empty($this->options['not']) ? '' : " OR $this->tableAlias.$this->realField IS NULL";

    if ($uid = reset($this->value)) {
      $user = \Drupal::currentUser();

      if ($user->id() == $uid) {
        $operator = empty($this->options['not']) ? '=' : '!=';
        $this->query->addWhereExpression(
          0,
          "$this->tableAlias.$this->realField $operator $placeholder" . $null_check,
          [
            $placeholder => $this->argument,
          ]
        );
      }
    }
  }

}
