<?php

/**
 * @file
 * Contains social_media_post.page.inc.
 *
 * Page callback for Oma entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Oma entity templates.
 *
 * Default template: social_media_post.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_social_media_post(array &$variables) {
  // Fetch OmaEntity Entity Object.
  $social_media_post = $variables['elements']['#social_media_post'];
  $social_media_type = $social_media_post->getType();
  $social_media_type_class = $social_media_type;
  // Change the class for youtube so it's not the default icon.
  if ($social_media_type === 'youtube') {
    $social_media_type_class = 'youtube-play';
  }

  $variables['social_media_type_class'] = $social_media_type_class;

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
