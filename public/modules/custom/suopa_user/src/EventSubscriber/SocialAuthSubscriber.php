<?php

namespace Drupal\suopa_user\EventSubscriber;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\social_auth\Event\UserEvent;
use Drupal\social_auth\Event\SocialAuthEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Reacts on Social Auth events.
 */
class SocialAuthSubscriber implements EventSubscriberInterface {

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  private $messenger;

  /**
   * SocialAuthSubscriber constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   *
   * Returns an array of event names this subscriber wants to listen to.
   * For this case, we are going to subscribe for user creation and login
   * events and call the methods to react on these events.
   */
  public static function getSubscribedEvents() {
    $events[SocialAuthEvents::USER_CREATED] = ['onUserCreated'];
    $events[SocialAuthEvents::USER_LOGIN] = ['onUserLogin'];

    return $events;
  }

  /**
   * Adds user real name.
   *
   * @param \Drupal\social_auth\Event\UserEvent $event
   *   The Social Auth user event object.
   */
  public function onUserCreated(UserEvent $event) {

    /*
     * @var Drupal\user\UserInterface $user
     */
    $user = $event->getUser();
    $social_auth_user = $event->getSocialAuthUser();

    // Set user first name if available.
    if ($social_auth_user->getFirstName()) {
      $user->set('field_first_name', $social_auth_user->getFirstName());
    }

    // Set user last name if available.
    if ($social_auth_user->getLastName()) {
      $user->set('field_last_name', $social_auth_user->getLastName());
    }
  }

  /**
   * Alters the user real name.
   *
   * @param \Drupal\social_auth\Event\UserEvent $event
   *   The Social Auth user event object.
   */
  public function onUserLogin(UserEvent $event) {

    /*
     * @var Drupal\user\UserInterface $user
     */
    $user = $event->getUser();
    $social_auth_user = $event->getSocialAuthUser();
    $setUserAuthorReference = FALSE;

    // Set user first name if available.
    if (
      $user->hasField('field_first_name') &&
      empty($user->field_first_name->value)
    ) {
      if ($social_auth_user->getFirstName()) {
        $user->set('field_first_name', $social_auth_user->getFirstName());
        $setUserAuthorReference = TRUE;
      }
    }

    // Set user first name if available.
    if (
      $user->hasField('field_last_name') &&
      empty($user->field_last_name->value)
    ) {
      if ($social_auth_user->getLastName()) {
        $user->set('field_last_name', $social_auth_user->getLastName());
        $setUserAuthorReference = TRUE;
      }
    }

    // Set user - author reference if needed.
    if ($setUserAuthorReference) {
      $author_service = \Drupal::service('suopa_editorial.apply_author');
      $author_service->createReference($user);
    }
  }

}
