<?php

namespace Drupal\suopa_editorial;

use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;

/**
 * AuthorService creates a reference between author term and user account.
 */
class AuthorService {

  /**
   * The account to be linked to author taxonomy.
   *
   * @var \Drupal\user\Entity\User
   */
  private $account;

  /**
   * The full name of the user.
   *
   * @var string
   */
  private $fullName;

  /**
   * The taxonomy term from author vocabulary.
   *
   * @var \Drupal\taxonomy\Entity\Term
   */
  private $author;

  /**
   * The author account id, if available.
   *
   * @var string
   */
  private $authorAccountId;

  /**
   * If account is available, try to apply reference to author term.
   *
   * @param \Drupal\user\Entity\User $account
   *   User account.
   */
  public function createOrUpdateReference(User $account) {
    if ($account->id()) {
      $this->account = $account;
      $this->generateFullName();
      $this->applyReference();
    }
  }

  /**
   * Create full name based on user input.
   */
  private function generateFullName() {
    if ($this->account->hasField('field_first_name')) {
      $first_name = ucfirst($this->account->field_first_name->value);
    }
    if ($this->account->hasField('field_last_name')) {
      $last_name = ucfirst($this->account->field_last_name->value);
    }

    $this->fullName = $first_name . ' ' . $last_name;
  }

  /**
   * Apply existing reference to author term or create a new one.
   */
  private function applyReference() {
    $this->retrieveAuthor();

    if ($this->author) {
      $this->handleReference();
    }
    else {
      $this->createNewReference(TRUE);
    }
  }

  /**
   * Check if author exists already.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   */
  private function retrieveAuthor() {
    $author = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties([
        'name' => $this->fullName,
        'vid' => 'author',
      ]);

    // Account is found from the author taxonomy.
    if (is_array($author) && !empty($author)) {
      $this->author = reset($author);

      if (!is_null($this->author->field_reference_to_user_profile->target_id)) {
        $this->authorAccountId = $this->author->field_reference_to_user_profile->target_id;
      }
    }
  }

  /**
   * Handles the reference between account and author taxonomy term.
   */
  private function handleReference() {
    // Link account to author term.
    if (!$this->authorAccountId && $this->author) {
      $this->author->field_reference_to_user_profile->target_id = $this->account->id();
      $this->author->save();
      $this->logMessage(
        'New author - account reference: @author_term was linked to %account_id.',
        [
          '%author_term' => 'author ' . $this->author->id() . ': ' . $this->author->name->value,
          '%account_id' => 'account ' . $this->account->id() . ': ' . $this->fullName,
        ]
      );
    }
    else {
      // User with a same name already exists in the database. Create new
      // term and link the term to user account.
      $this->createNewReference();
    }
  }

  /**
   * Creates a new author taxonomy term and links user account to it.
   *
   * @param bool $newAuthor
   *   Indicates whether the author exists or is new.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Thrown if storage operations fail.
   */
  private function createNewReference($newAuthor = FALSE) {
    $this->author = Term::create([
      'name' => $this->fullName,
      'vid' => 'author',
      'field_reference_to_user_profile' => $this->account->id(),
    ]);
    $this->author->save();

    if ($newAuthor) {
      $this->logMessage(
        'New author - account reference: @old_author_term was found from the author taxonomy. New reference between %author_term and %account_id was created.',
        [
          '%old_author_term' => 'author ' . $this->authorAccountId,
          '%author_term' => 'author ' . $this->author->id() . ': ' . $this->author->name->value,
          '%account_id' => 'account ' . $this->account->id() . ': ' . $this->fullName,
        ]
      );
    }
    else {
      $this->logMessage(
        'New author - account reference: %author_term and %account_id was created.',
        [
          '%author_term' => 'author ' . $this->author->id() . ': ' . $this->author->name->value,
          '%account_id' => 'account ' . $this->account->id() . ': ' . $this->fullName,
        ]
      );
    }
  }

  /**
   * Log a message.
   *
   * @param string $message
   *   Message to save to the log.
   * @param array $variables
   *   Variables to be included to the message.
   */
  private function logMessage($message, array $variables) {
    \Drupal::logger('Author Taxonomy Reference')->notice($message, $variables);
  }

}
