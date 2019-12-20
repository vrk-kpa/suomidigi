<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia;

use MetzWeb\Instagram\Instagram;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\social_media_import\Entity\SocialMediaPost;
use Drupal\social_media_import\Entity\InstagramFeed;

/**
 * Instagram importer.
 */
final class InstagramImporter {

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
   * Instagram Client.
   *
   * @var \MetzWeb\Instagram\Instagram
   */
  private $instagram;

  /**
   * Instagram Client ID.
   *
   * @var string
   */
  private $clientId;

  /**
   * Instagram Client ID.
   *
   * @var string
   */
  private $clientSecret;

  /**
   * Redirect URI for Instagram.
   *
   * @var string
   */
  private $redirectUri;

  /**
   * Instagram Client access token.
   *
   * @var string
   */
  private $accessToken;

  /**
   * Instagram User ID.
   *
   * @var string
   */
  private $userId;

  /**
   * Post counter.
   *
   * @var string
   */
  private $counter;

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
   */
  public function setClient() {
    if (NULL === $this->instagram) {
      $config = $this->configFactory->get('social_media_import.instagram_api_settings');
      $this->clientId = $config->get('client_id');
      $this->clientSecret = $config->get('client_secret');
      $this->accessToken = $config->get('auth_token');
      $this->redirectUri = $config->get('redirect_uri');
      $this->instagram = new Instagram([
        'apiKey' => $this->clientId,
        'apiSecret' => $this->clientSecret,
        'apiCallback' => $this->redirectUri,
      ]);
      $this->instagram->setAccessToken($this->accessToken);
    }
  }

  /**
   * Gets the feed for the user ID.
   *
   * @return array
   *   Retrieved results.
   *
   * @throws \Exception
   */
  private function getFeed() :array {
    $posts = [];
    $postsLimit = 20;
    $response = $this->instagram->getUserMedia($this->userId, $postsLimit);
    $resolution = 'standard_resolution';

    if (isset($response->data)) {
      $posts = array_map(function ($post) use ($resolution) {
        $type = $this->getMediaArrayKey($post->type);
        return [
          'raw'       => $post,
          'media_url' => isset($post->{'images'}->{$resolution}) ? $post->{'images'}->{$resolution}->url : '',
          'type'      => $post->type,
        ];
      }, $response->data);
    }

    return $posts;
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
        'user_id' => $this->userId,
        'type' => 'instagram',
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
   *   Tweet ID.
   *
   * @return array|false
   *   Retrieved entity or false
   */
  protected function getEntity($id) {
    $entities = \Drupal::entityTypeManager()
      ->getStorage('social_media_post')
      ->loadByProperties([
        'user_id' => $this->userId,
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
      ->loadByProperties(['post_id' => $post['raw']->id]);

    if (!$entity_exists) {
      $entity = [
        'type' => 'instagram',
        'user_id' => $this->userId,
        'feed_id' => $feed_id,
        'post_id' => $post['raw']->id,
        'full_name' => $this->fullName,
        'text' => $post['raw']->caption->text,
        'author_screen_name' => $post['raw']->user->username,
        'created' => $post['raw']->created_time,
        'link' => [
          'uri' => $post['raw']->link,
          'title' => $post['raw']->link,
          'options' => ['target' => '_blank'],
        ],
      ];
      // Separate handler for media.
      if (!empty($post['media_url'])) {
        $entity['image'] = social_media_import_save_file($post['media_url'], 'public://instagram/');
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
   * Retrieve the array key to fetch the post media url.
   *
   * @param string $type
   *   The post type.
   *
   * @return string
   *   The array key to fetch post media url.
   */
  protected function getMediaArrayKey($type) {
    $mediaType = 'images';
    if ($type === 'video') {
      $mediaType = 'videos';
    }
    return $mediaType;
  }

  /**
   * Refresh the instagram feed.
   *
   * @param \Drupal\social_media_import\Entity\InstagramFeed $feed
   *   Feed to be refreshed.
   */
  public function refresh(InstagramFeed $feed) {
    $this->counter = 0;
    $this->userId = $feed->getUserId();
    $posts = $this->getFeed();
    $retrieved_posts = [];

    foreach ($posts as $post) {
      $retrieved_posts[] = $post['raw']->id;
      $this->fullName = $post['raw']->user->full_name;
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
