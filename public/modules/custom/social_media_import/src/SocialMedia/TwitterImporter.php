<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia;

use Abraham\TwitterOAuth\TwitterOAuth;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\social_media_import\Entity\SocialMediaPost;
use Drupal\social_media_import\Entity\TwitterFeed;

/**
 * Twitter importer.
 */
final class TwitterImporter {

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
   * Twitter Client.
   *
   * @var \Abraham\TwitterOAuth\TwitterOAuth
   */
  private $twitter;

  /**
   * Twitter Screen name.
   *
   * @var string
   */
  private $screenName;

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
    if (NULL === $this->twitter) {
      $config = $this->configFactory->get('social_media_import.twitter_api_settings');
      $this->twitter = new TwitterOAuth(
        $config->get('consumer_key'),
        $config->get('consumer_secret')
      );
    }
  }

  /**
   * Gets the feed for the screen name.
   *
   * @param string $screenName
   *   Screen name to retrieve the feed with.
   *
   * @return array
   *   Retrieved results.
   *
   * @throws \Exception
   */
  private function getFeed(string $screenName) :array {
    $results = $this->twitter->get(
      'statuses/user_timeline',
      [
        'screen_name' => $screenName,
        'count' => 20,
        'tweet_mode' => 'extended',
      ]
    );

    if ($this->twitter->getLastHttpCode() !== 200) {
      throw new \Exception(sprintf('Failed to retrieve tweets for account "%s"', $screenName));
    }

    return $results;
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
        'author_screen_name' => $this->screenName,
        'type' => 'twitter',
      ]);
    foreach ($entities as $entity) {
      $return[current($entity->get('id')->getValue())['value']] = current($entity->get('tweet_id')->getValue())['value'];
    }
    return $return;
  }

  /**
   * Get entity by tweet id.
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
        'author_screen_name' => $this->screenName,
        'tweet_id' => $id,
      ]);

    if (!empty($entities)) {
      $return = [];
      foreach ($entities as &$entity) {
        $return[current($entity->get('id')->getValue())['value']] = current($entity->get('tweet_id')->getValue())['value'];
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
      ->loadByProperties(['tweet_id' => $post->id]);

    // $media = (isset($post->entities->media[0])) ? TRUE : FALSE;
    $url = 'https://twitter.com/' . $post->user->screen_name . '/status/' . $post->id;

    $text = $this->addTweetEntityLinks($post);
    $profile_image = (isset($post->user->profile_image_url)) ? $post->user->profile_image_url : NULL;

    if (!$entity_exists) {
      $entity = [
        'type' => 'twitter',
        'feed_id' => $feed_id,
        'tweet_id' => $post->id,
        'full_name' => $this->fullName,
        'text' => $text,
        'author_screen_name' => $post->user->screen_name,
        'created' => strtotime($post->created_at),
        'link' => [
          'uri' => $url,
          'title' => $url,
          'options' => ['target' => '_blank'],
        ],
        'profile_image' => $profile_image,
      ];
      // Separate handler for media.
      /*
      if ($media) {
      $entity['image'] = social_media_import_save_file(
      $post->entities->media[0]->media_url_https, 'public://twitter/'
      );
      } */
      SocialMediaPost::create($entity)->save();
      $this->counter++;
    }
  }

  /**
   * Delete Social media post.
   *
   * @param array $tweets
   *   Tweet ID of the deleted entity.
   */
  protected function deleteEntities(array $tweets) {
    // Check if post exists.
    if (!empty($tweets)) {
      // Load entities.
      $entities = \Drupal::entityTypeManager()
        ->getStorage('social_media_post')
        ->loadByProperties(['tweet_id' => $tweets]);

      foreach ($entities as $entity) {
        $entity->delete();
      }
    }
  }

  /**
   * Refresh the twitter feed.
   *
   * @param \Drupal\social_media_import\Entity\TwitterFeed $feed
   *   Feed to be refreshed.
   */
  public function refresh(TwitterFeed $feed) {
    // @todo check if posts need to be updated. Limit this with a reasonable setting, for example 1h.
    $this->screenName = $feed->getScreenName();
    $posts = $this->getFeed($this->screenName);

    // @todo store feed hash to state to allow quick comparison between the current state and the 3rd party service state.
    $postsWithoutReplies = array_filter($posts, function ($post) {
      return empty($post->in_reply_to_status_id);
    });

    $retrieved_posts = [];
    // @todo test if post has already been imported.
    foreach ($postsWithoutReplies as $post) {
      $retrieved_posts[] = $post->id;
      $this->fullName = $post->user->name;
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

  /**
   * Convert strings utf8 compatible.
   *
   * @param string $original
   *   Original text.
   * @param string $replacement
   *   Replacement text.
   * @param int $position
   *   Position of the text.
   * @param int $length
   *   Length.
   *
   * @return string
   *   Return converted text.
   */
  private function utf8SubstrReplace($original, $replacement, $position, $length) {
    $startString = mb_substr($original, 0, $position, "UTF-8");
    $endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");
    $out = $startString . $replacement . $endString;
    return $out;
  }

  /**
   * Convert hashtags, urls and users to links.
   *
   * @param object $tweet
   *   Tweet object.
   *
   * @return string
   *   Returns converted string.
   */
  private function addTweetEntityLinks($tweet) {
    $return = mb_substr($tweet->full_text, $tweet->display_text_range[0], $tweet->display_text_range[1]);
    $entities = [];

    if (is_array($tweet->entities->urls)) {
      foreach ($tweet->entities->urls as $e) {
        $temp["start"] = $e->indices[0];
        $temp["end"] = $e->indices[1];
        $temp["replacement"] = " <a href='" . $e->expanded_url . "' target='_blank'>" . $e->display_url . "</a>";
        $entities[] = $temp;
      }
    }

    if (is_array($tweet->entities->user_mentions)) {
      foreach ($tweet->entities->user_mentions as $e) {
        $temp["start"] = $e->indices[0];
        $temp["end"] = $e->indices[1];
        $temp["replacement"] = " <a href='https://twitter.com/" . $e->screen_name . "' target='_blank'>@" . $e->screen_name . "</a>";
        $entities[] = $temp;
      }
    }

    if (is_array($tweet->entities->hashtags)) {
      foreach ($tweet->entities->hashtags as $e) {
        $temp["start"] = $e->indices[0];
        $temp["end"] = $e->indices[1];
        $temp["replacement"] = " <a href='https://twitter.com/hashtag/" . $e->text . "?src=hash' target='_blank'>#" . $e->text . "</a>";
        $entities[] = $temp;
      }
    }

    usort($entities, function ($a, $b) {
      return($b["start"] - $a["start"]);
    });

    foreach ($entities as $item) {
      $return = $this->utf8SubstrReplace($return, $item["replacement"], $item["start"], $item["end"] - $item["start"]);
    }

    return($return);
  }

}
