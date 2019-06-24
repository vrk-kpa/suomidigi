<?php

namespace Drupal\suopa_external_files\Plugin\media\Source;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;

/**
 * External file entity media source.
 *
 * @MediaSource(
 *   id = "externalfile",
 *   label = @Translation("External file"),
 *   allowed_field_types = {"string"},
 *   default_thumbnail_filename = "externalfile.png",
 *   description = @Translation("Provides business logic and metadata for External file.")
 * )
 */
class ExternalFile extends MediaSourceBase {

  /**
   * Config factory interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * URL to file.
   */
  protected $fileURL;

  /**
   * Media ID.
   */
  protected $mediaID;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, FieldTypePluginManagerInterface $field_type_manager, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $entity_field_manager, $field_type_manager, $config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataAttributes() {
    $attributes = [
      'name' => $this->t('Name'),
      'thumbnail_uri' => t('URI of the thumbnail'),
    ];
    return $attributes;
  }

  /**
   * {@inheritdoc}
   */

  public function getMetadata(MediaInterface $media, $attribute_name) {
    switch ($attribute_name) {
      case 'thumbnail_uri':
        return 'public://media-icons/generic/externalfile.png';

      default:
        return parent::getMetadata($media, $attribute_name);
    }
  }

  /**
   * Gather file metadata.
   *
   * @param \Drupal\media\MediaInterface $media
   *   Media (file).
   *
   * @return array|bool
   *   Returns an array of file data or false.
   */
  public function getFileMetadata(MediaInterface $media) {
    if ($media->hasField('field_media_externalfile')) {
      $this->mediaID = $media->id();
      $this->fileURL = $media->get('field_media_externalfile')->value;
      return $this->getCachedFileData();
    }

    return FALSE;
  }

  /**
   * Get cached file metadata.
   *
   * @return array|bool
   *   Returns an array of file data or false.
   */
  public function getCachedFileData() {
    if (!empty($this->fileURL)) {
      $data = &drupal_static(__METHOD__);
      $cid = 'suopa_external_file_types:' . \Drupal::languageManager()->getCurrentLanguage()->getId() . ':' . $this->mediaID;

      if ($cache = \Drupal::cache()->get($cid)) {
        $data = $cache->data;
      }

      if (empty($data) || $data['file_url'] !== $this->fileURL) {
        $data = $this->getExternalFileData($this->fileURL);
        \Drupal::cache()->set($cid, $data);
      }

      return $data;
    }

    return FALSE;
  }

  /**
   * Get file metadata without downloading the actual file.
   *
   * @param $url
   *   URL to check the file metadata for.
   *
   * @return array
   *   Return an array of filedata.
   */
  public function getExternalFileData($url) {
    $file_data = [];

    $curl = curl_init( $url );
    curl_setopt( $curl, CURLOPT_NOBODY, true );
    curl_setopt( $curl, CURLOPT_HEADER, true );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
    $data = curl_exec( $curl );
    $mime = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    curl_close( $curl );

    if ($data) {
      if (preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches )) {
        $status = (int) $matches[1];
      }

      if (preg_match( "/Content-Length: (\d+)/", $data, $matches )) {
        $content_length = (int) $matches[1];
      }

      if ($status == 200 || ($status > 300 && $status <= 308)) {
        $file_data['file_size'] = $content_length;
        $file_data['file_type'] = $this->mimeToExt($mime);
        $file_data['file_url'] = $url;
      }
    }

    return $file_data;
  }

  /**
   * Map mime types to file types.
   * @param $mime
   *   Mime type.
   *
   * @return mixed|string
   *   Returns mapped file type or empty string.
   */
  private function mimeToExt($mime) {
    $mime_map = [
      'video/3gpp2' => '3g2',
      'video/3gp' => '3gp',
      'video/3gpp' => '3gp',
      'application/x-compressed' => '7zip',
      'audio/x-acc' => 'aac',
      'audio/ac3' => 'ac3',
      'application/postscript' => 'ai',
      'audio/x-aiff' => 'aif',
      'audio/aiff' => 'aif',
      'audio/x-au' => 'au',
      'video/x-msvideo' => 'avi',
      'video/msvideo' => 'avi',
      'video/avi' => 'avi',
      'application/x-troff-msvideo' => 'avi',
      'application/macbinary' => 'bin',
      'application/mac-binary' => 'bin',
      'application/x-binary' => 'bin',
      'application/x-macbinary' => 'bin',
      'image/bmp' => 'bmp',
      'image/x-bmp' => 'bmp',
      'image/x-bitmap' => 'bmp',
      'image/x-xbitmap' => 'bmp',
      'image/x-win-bitmap' => 'bmp',
      'image/x-windows-bmp' => 'bmp',
      'image/ms-bmp' => 'bmp',
      'image/x-ms-bmp' => 'bmp',
      'application/bmp' => 'bmp',
      'application/x-bmp' => 'bmp',
      'application/x-win-bitmap' => 'bmp',
      'application/cdr' => 'cdr',
      'application/coreldraw' => 'cdr',
      'application/x-cdr' => 'cdr',
      'application/x-coreldraw' => 'cdr',
      'image/cdr' => 'cdr',
      'image/x-cdr' => 'cdr',
      'zz-application/zz-winassoc-cdr' => 'cdr',
      'application/mac-compactpro' => 'cpt',
      'application/pkix-crl' => 'crl',
      'application/pkcs-crl' => 'crl',
      'application/x-x509-ca-cert' => 'crt',
      'application/pkix-cert' => 'crt',
      'text/css' => 'css',
      'text/x-comma-separated-values' => 'csv',
      'text/comma-separated-values' => 'csv',
      'application/vnd.msexcel' => 'csv',
      'application/x-director' => 'dcr',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
      'application/x-dvi' => 'dvi',
      'message/rfc822' => 'eml',
      'application/x-msdownload' => 'exe',
      'video/x-f4v' => 'f4v',
      'audio/x-flac' => 'flac',
      'video/x-flv' => 'flv',
      'image/gif' => 'gif',
      'application/gpg-keys' => 'gpg',
      'application/x-gtar' => 'gtar',
      'application/x-gzip' => 'gzip',
      'application/mac-binhex40' => 'hqx',
      'application/mac-binhex' => 'hqx',
      'application/x-binhex40' => 'hqx',
      'application/x-mac-binhex40' => 'hqx',
      'text/html' => 'html',
      'image/x-icon' => 'ico',
      'image/x-ico' => 'ico',
      'image/vnd.microsoft.icon' => 'ico',
      'text/calendar' => 'ics',
      'application/java-archive' => 'jar',
      'application/x-java-application' => 'jar',
      'application/x-jar' => 'jar',
      'image/jp2' => 'jp2',
      'video/mj2' => 'jp2',
      'image/jpx' => 'jp2',
      'image/jpm' => 'jp2',
      'image/jpeg' => 'jpeg',
      'image/pjpeg' => 'jpeg',
      'application/x-javascript' => 'js',
      'application/json' => 'json',
      'text/json' => 'json',
      'application/vnd.google-earth.kml+xml' => 'kml',
      'application/vnd.google-earth.kmz' => 'kmz',
      'text/x-log' => 'log',
      'audio/x-m4a' => 'm4a',
      'audio/mp4' => 'm4a',
      'application/vnd.mpegurl' => 'm4u',
      'audio/midi' => 'mid',
      'application/vnd.mif' => 'mif',
      'video/quicktime' => 'mov',
      'video/x-sgi-movie' => 'movie',
      'audio/mpeg' => 'mp3',
      'audio/mpg' => 'mp3',
      'audio/mpeg3' => 'mp3',
      'audio/mp3' => 'mp3',
      'video/mp4' => 'mp4',
      'video/mpeg' => 'mpeg',
      'application/oda' => 'oda',
      'audio/ogg' => 'ogg',
      'video/ogg' => 'ogg',
      'application/ogg' => 'ogg',
      'application/x-pkcs10' => 'p10',
      'application/pkcs10' => 'p10',
      'application/x-pkcs12' => 'p12',
      'application/x-pkcs7-signature' => 'p7a',
      'application/pkcs7-mime' => 'p7c',
      'application/x-pkcs7-mime' => 'p7c',
      'application/x-pkcs7-certreqresp' => 'p7r',
      'application/pkcs7-signature' => 'p7s',
      'application/pdf' => 'pdf',
      'application/octet-stream' => 'pdf',
      'application/x-x509-user-cert' => 'pem',
      'application/x-pem-file' => 'pem',
      'application/pgp' => 'pgp',
      'application/x-httpd-php' => 'php',
      'application/php' => 'php',
      'application/x-php' => 'php',
      'text/php' => 'php',
      'text/x-php' => 'php',
      'application/x-httpd-php-source' => 'php',
      'image/png' => 'png',
      'image/x-png' => 'png',
      'application/powerpoint' => 'ppt',
      'application/vnd.ms-powerpoint' => 'ppt',
      'application/vnd.ms-office' => 'ppt',
      'application/msword' => 'ppt',
      'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
      'application/x-photoshop' => 'psd',
      'image/vnd.adobe.photoshop' => 'psd',
      'audio/x-realaudio' => 'ra',
      'audio/x-pn-realaudio' => 'ram',
      'application/x-rar' => 'rar',
      'application/rar' => 'rar',
      'application/x-rar-compressed' => 'rar',
      'audio/x-pn-realaudio-plugin' => 'rpm',
      'application/x-pkcs7' => 'rsa',
      'text/rtf' => 'rtf',
      'text/richtext' => 'rtx',
      'video/vnd.rn-realvideo' => 'rv',
      'application/x-stuffit' => 'sit',
      'application/smil' => 'smil',
      'text/srt' => 'srt',
      'image/svg+xml' => 'svg',
      'application/x-shockwave-flash' => 'swf',
      'application/x-tar' => 'tar',
      'application/x-gzip-compressed' => 'tgz',
      'image/tiff' => 'tiff',
      'text/plain' => 'txt',
      'text/x-vcard' => 'vcf',
      'application/videolan' => 'vlc',
      'text/vtt' => 'vtt',
      'audio/x-wav' => 'wav',
      'audio/wave' => 'wav',
      'audio/wav' => 'wav',
      'application/wbxml' => 'wbxml',
      'video/webm' => 'webm',
      'audio/x-ms-wma' => 'wma',
      'application/wmlc' => 'wmlc',
      'video/x-ms-wmv' => 'wmv',
      'video/x-ms-asf' => 'wmv',
      'application/xhtml+xml' => 'xhtml',
      'application/excel' => 'xl',
      'application/msexcel' => 'xls',
      'application/x-msexcel' => 'xls',
      'application/x-ms-excel' => 'xls',
      'application/x-excel' => 'xls',
      'application/x-dos_ms_excel' => 'xls',
      'application/xls' => 'xls',
      'application/x-xls' => 'xls',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
      'application/vnd.ms-excel' => 'xlsx',
      'application/xml' => 'xml',
      'text/xml' => 'xml',
      'text/xsl' => 'xsl',
      'application/xspf+xml' => 'xspf',
      'application/x-compress' => 'z',
      'application/x-zip' => 'zip',
      'application/zip' => 'zip',
      'application/x-zip-compressed' => 'zip',
      'application/s-compressed' => 'zip',
      'multipart/x-zip' => 'zip',
      'text/x-scriptzsh' => 'zsh',
    ];

    return isset($mime_map[$mime]) ? $mime_map[$mime] : '';
  }
}
