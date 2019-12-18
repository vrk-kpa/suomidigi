<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\social_media_import\Entity\SocialMediaPost;
use Drupal\social_media_import\Entity\FacebookFeed;

/**
 * Facebook importer.
 */
final class FacebookImporter {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactory;

  /**
   * The state.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  private $state;

  /**
   * Field names to retrieve from Facebook.
   *
   * @var array
   */
  protected $fields = [
    'link',
    'message',
    'created_time',
    'full_picture',
    'type',
    'actions',
    'about',
  ];

  /**
   * Facebook Client.
   *
   * @var \Facebook\Facebook
   */
  private $facebook;

  /**
   * Post counter.
   *
   * @var string
   */
  private $counter;

  /**
   * Facebook page name.
   *
   * @var string
   */
  private $pageName;

  /**
   * Full name.
   *
   * @var string
   */
  private $fullName;

  /**
   * Constructs a new instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state.
   */
  public function __construct(ConfigFactoryInterface $configFactory, StateInterface $state) {
    $this->configFactory = $configFactory;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Facebook\Exceptions\FacebookSDKException
   */
  public function setClient() {
    if (NULL === $this->facebook) {
      $config = $this->configFactory->get('social_media_import.facebook_api_settings');
      $this->facebook = new Facebook([
        'app_id' => $config->get('app_id'),
        'app_secret' => $config->get('app_secret'),
        'default_graph_version' => 'v2.10',
        'default_access_token' => $config->get('auth_token'),
      ]);
    }
  }

  /**
   * Gets the feed for the page name.
   *
   * @return array
   *   Retrieved results.
   *
   * @throws \Exception
   */
  private function getFeed() :array {
    $posts = [];
    $postsLimit = 20;
    $config = $this->configFactory->get('social_media_import.facebook_api_settings');

    try {
      $response = $this->facebook->get(
        $this->getFacebookFeedUrl($postsLimit),
        $config->get('app_id') . '|' . $config->get('app_secret')
      );
    }
    catch (FacebookResponseException $e) {
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    }
    catch (FacebookSDKException $e) {
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    try {
      $pageResponse = $this->facebook->get(
        "/{$this->pageName}?fields=name",
        $config->get('app_id') . '|' . $config->get('app_secret')
      );
    }
    catch (FacebookResponseException $e) {
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    }
    catch (FacebookSDKException $e) {
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    $this->fullName = $pageResponse->getDecodedBody()['name'];

    return $response->getDecodedBody();
  }

  /**
   * Create the Facebook feed URL.
   *
   * @param int $postsLimit
   *   The number of posts to return.
   *
   * @return string
   *   The feed URL with the appended fields to retrieve.
   */
  protected function getFacebookFeedUrl($postsLimit) {
    return '/' . $this->pageName . '/feed?fields=' . implode(',', $this->fields) . '&limit=' . $postsLimit;
  }

  /**
   * Get all entities by screen name.
   *
   * @return array|null
   *   Retrieved entities or null
   */
  protected function getAllEntities() {
    $return = [];
    $entities = \Drupal::entityTypeManager()
      ->getStorage('social_media_post')
      ->loadByProperties([
        'page_name' => $this->pageName,
        'type' => 'facebook',
      ]);
    foreach ($entities as $entity) {
      $return[current($entity->get('id')->getValue())['value']] = current($entity->get('post_id')->getValue())['value'];
    }
    return $return;
  }

  /**
   * Get entity by post id.
   *
   * @param string $id
   *   Post ID.
   *
   * @return array|false
   *   Retrieved entity or false
   */
  protected function getEntity($id) {
    $entities = \Drupal::entityTypeManager()
      ->getStorage('social_media_post')
      ->loadByProperties([
        'page_name' => $this->pageName,
        'post_id' => $id,
      ]);

    if (!empty($entities)) {
      $return = [];
      foreach ($entities as &$entity) {
        $return[current($entity->get('id')->getValue())['value']] = current($entity->get('post_id')->getValue())['value'];
      }
      return $return;
    }
    return FALSE;
  }

  /**
   * Save Social media post.
   *
   * @param object $post
   *   Post to be saved.
   * @param string $feed_id
   *   Feed ID to be saved to the post.
   */
  protected function saveEntity($post, $feed_id) {
    // Check if post exists.
    $entity_exists = \Drupal::entityTypeManager()
      ->getStorage('social_media_post')
      ->loadByProperties(['post_id' => $post['id']]);

    if (!$entity_exists && isset($post['message'])) {
      $entity = [
        'type' => 'facebook',
        'page_name' => $this->pageName,
        'feed_id' => $feed_id,
        'post_id' => $post['id'],
        'full_name' => $this->fullName,
        'text' => $post['message'],
        'created' => strtotime($post['created_time']),
        'link' => [
          'uri' => $post['link'],
          'title' => $post['link'],
          'options' => ['target' => '_blank'],
        ],
      ];
      // Separate handler for media.
      if (!empty($post['full_picture'])) {
        $entity['image'] = social_media_import_save_file($post['full_picture'], 'public://facebook/');
      }
      SocialMediaPost::create($entity)->save();
      $this->counter++;
    }
  }

  /**
   * Delete Social media post.
   *
   * @param array $posts
   *   Posts which should be deleted from database.
   */
  protected function deleteEntities(array $posts) {
    // Check if post exists.
    if (!empty($posts)) {
      // Load entities.
      $entities = \Drupal::entityTypeManager()
        ->getStorage('social_media_post')
        ->loadByProperties(['post_id' => $posts]);

      foreach ($entities as $entity) {
        $entity->delete();
      }
    }
  }

  /**
   * Refresh the facebook feed.
   *
   * @param \Drupal\social_media_import\Entity\FacebookFeed $feed
   *   Feed to be refreshed.
   */
  public function refresh(FacebookFeed $feed) {
    $this->counter = 0;
    $this->pageName = $feed->getPageName();
    $posts = $this->getFeed();
    $retrieved_posts = [];

    foreach ($posts['data'] as $post) {
      $retrieved_posts[] = $post['id'];
      $this->saveEntity($post, $feed->id());
    }

    // Remove the older entities.
    $this->deleteEntities(array_diff($this->getAllEntities(), $retrieved_posts));

    // Log the import.
    \Drupal::logger('social_media_import')->notice('@type: Added @counter posts.',
      [
        '@type' => $feed->getEntityTypeId(),
        '@counter' => $this->counter,
      ]
    );
  }

}
