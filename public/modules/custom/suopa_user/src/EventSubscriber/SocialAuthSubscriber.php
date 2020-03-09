<?php

namespace Drupal\suopa_user\EventSubscriber;

use Drupal\suopa_editorial\AuthorService;
use Drupal\social_auth\Event\UserEvent;
use Drupal\social_auth\Event\SocialAuthEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Reacts on Social Auth events.
 */
class SocialAuthSubscriber implements EventSubscriberInterface {

  /**
   * The author service.
   *
   * @var \Drupal\suopa_editorial\AuthorService
   */
  private $authorService;

  /**
   * SocialAuthSubscriber constructor.
   *
   * @param \Drupal\suopa_editorial\AuthorService $author_service
   *   The author service.
   */
  public function __construct(AuthorService $author_service) {
    $this->authorService = $author_service;
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
    return $events;
  }

  /**
   * Adds user real name.
   *
   * @param \Drupal\social_auth\Event\UserEvent $event
   *   The Social Auth user event object.
   */
  public function onUserCreated(UserEvent $event) {
    $user = $event->getUser();
    $social_auth_user = $event->getSocialAuthUser();

    // Set user first and last name if available.
    if ($name = $this->splitName($social_auth_user->getName())) {
      $user->set('field_first_name', $name['first_name']);
      $user->set('field_last_name', $name['last_name']);
      $user->save();
      $this->authorService->createReference($user);
    }
  }

  /**
   * Splits single name string into salutation, first, last, suffix
   *
   * @param string $name
   *
   * @return array
   */
  private function splitName($name) {
    $results = [];

    $r = explode(' ', $name);
    $size = count($r);

    // Check first for salutation.
    if (mb_strpos($r[0], '.') === false) {
      $results['salutation'] = '';
      $results['first_name'] = $r[0];
    }
    else {
      $results['salutation'] = $r[0];
      $results['first_name'] = $r[1];
    }

    // Check last for period, assume suffix if so.
    if (mb_strpos($r[$size - 1], '.') === false) {
      $results['suffix'] = '';
    }
    else {
      $results['suffix'] = $r[$size - 1];
    }

    // Combine remains into last.
    $start = ($results['salutation']) ? 2 : 1;
    $end = ($results['suffix']) ? $size - 2 : $size - 1;

    $last = '';
    for ($i = $start; $i <= $end; $i++) {
      $last .= ' '.$r[$i];
    }
    $results['last_name'] = trim($last);

    return $results;
  }

}
