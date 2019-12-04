<?php

namespace Drupal\suopa_blog_conversion\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Apply the automatic paragraph filter to content.
 *
 * @MigrateProcessPlugin(
 *   id = "wp_content"
 * )
 */
class WpContent extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return _filter_autop(
      $this->convertCaption(
        $this->convertImageUrls($value)
      )
    );
  }

  /**
   * Convert WP captions to div.
   *
   * @param string $content
   *   Content as string.
   *
   * @return string
   *   Returns same content without pesky captions.
   */
  private function convertCaption($content) {
    return preg_replace(
      '/\[caption([^\]]+)align="([^"]+)"\s+width="(\d+)"\](\s*\<img[^>]+>)\s*(.*?)\s*\[\/caption\]/i',
      '<div\1 class="wp-caption \2">\4<p class="caption">\5</p></div>',
      $content
    );
  }

  /**
   * Point images to local files instead of WP urls.
   *
   * @param string $content
   *   Content as string.
   *
   * @return string
   *   Returns same content without pesky captions.
   */
  private function convertImageUrls($content) {
    return preg_replace_callback(
      '/<img\s+.*?src=[\"\']?([^\"\' >]*)[\"\']?[^>]*>/i',
      [&$this, 'convertUrl'],
      $content
    );
  }

  /**
   * Static function for preg replace callback.
   *
   * @param array $src
   *   URL pieces.
   *
   * @return array
   *   Fixed URL pieces.
   */
  public static function convertUrl(array $src) {
    $url = end(array_keys($src));
    $src[$url] = '/sites/default/files/images/' . end(explode('/', $src[$url]));
    return $src;
  }

}
