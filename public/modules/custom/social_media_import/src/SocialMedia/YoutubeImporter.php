<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\social_media_import\Entity\SocialMediaPost;
use Drupal\social_media_import\Entity\YoutubeFeed;

/**
 * Youtube importer.
 */
final class YoutubeImporter {

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
   * Youtube Client.
   *
   * @var \GuzzleHttp\Client
   */
  private $youtube;

  /**
   * Youtube Screen name.
   *
   * @var string
   */
  private $channelName;

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
    if (NULL === $this->youtube) {
      $this->youtube = \Drupal::httpClient();
    }
  }

  /**
   * Gets the feed for the screen name.
   *
   * @param string $channelName
   *   Screen name to retrieve the feed with.
   *
   * @return array
   *   Retrieved results.
   *
   * @throws \Exception
   */
  private function getFeed(string $channelName) :array {
    $config = $this->configFactory->get('social_media_import.youtube_api_settings');
    $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&order=date&maxResults=20&channelId=' . $channelName . '&key=' . $config->get('google_api_key');
    $request = $this->youtube->get($url);

    if ($request->getStatusCode() !== 200) {
      throw new \Exception(sprintf('Failed to retrieve videos for account "%s"', $channelName));
    }

    $response = $request->getBody()->getContents();
    $results = json_decode($response, TRUE);

    return $results['items'];
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
        'channel_name' => $this->channelName,
        'type' => 'youtube',
      ]);
    foreach ($entities as $entity) {
      $return[current($entity->get('id')->getValue())['value']] = current($entity->get('video_id')->getValue())['value'];
    }
    return $return;
  }

  /**
   * Get entity by video id.
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
        'channel_name' => $this->channelName,
        'video_id' => $id,
      ]);

    if (!empty($entities)) {
      $return = [];
      foreach ($entities as &$entity) {
        $return[current($entity->get('id')->getValue())['value']] = current($entity->get('video_id')->getValue())['value'];
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
      ->loadByProperties(['video_id' => $post['id']['videoId']]);

    if (!$entity_exists) {
      $entity = [
        'type' => 'youtube',
        'feed_id' => $feed_id,
        'video_id' => $post['id']['videoId'],
        'full_name' => $this->fullName,
        'title' => $post['snippet']['title'],
        'text' => $post['snippet']['description'],
        'channel_name' => $post['snippet']['channelTitle'],
        'created' => strtotime($post['snippet']['publishedAt']),
        'link' => [
          'uri' => 'https://www.youtube.com/watch?v=' . $post['id']['videoId'],
          'title' => 'https://www.youtube.com/watch?v=' . $post['id']['videoId'],
          'options' => ['target' => '_blank'],
        ],
      ];
      // Separate handler for media.
      if (!empty($post['snippet']['thumbnails']['high']['url'])) {
        $entity['image'] = social_media_import_save_file($post['snippet']['thumbnails']['high']['url'], 'public://youtube/');
      }
      SocialMediaPost::create($entity)->save();
      $this->counter++;
    }
  }

  /**
   * Delete Social media post.
   *
   * @param array $videos
   *   Tweet ID of the deleted entity.
   */
  protected function deleteEntities(array $videos) {
    // Check if post exists.
    if (!empty($videos)) {
      // Load entities.
      $entities = \Drupal::entityTypeManager()
        ->getStorage('social_media_post')
        ->loadByProperties(['video_id' => $videos]);

      foreach ($entities as $entity) {
        $entity->delete();
      }
    }
  }

  /**
   * Refresh the youtube feed.
   *
   * @param \Drupal\social_media_import\Entity\YoutubeFeed $feed
   *   Feed to be refreshed.
   */
  public function refresh(YoutubeFeed $feed) {
    // @todo check if posts need to be updated. Limit this with a reasonable setting, for example 1h.
    $this->channelName = $feed->getChannelName();
    $posts = $this->getFeed($this->channelName);

    $retrieved_posts = [];
    // @todo test if post has already been imported.
    foreach ($posts as $post) {
      if ($post['id']['kind'] == 'youtube#video') {
        $retrieved_posts[] = $post['id']['videoId'];
        $this->fullName = $post['snippet']['channelTitle'];
        $this->saveEntity($post, $feed->id());
      }
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
