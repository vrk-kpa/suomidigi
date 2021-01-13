<?php

namespace Drupal\suopa_editorial\Commands;

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drush\Commands\DrushCommands;
use Drupal\Core\File\FileSystemInterface;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 */
class GenerateIconMediaEntities extends DrushCommands {

  /**
   * Files.
   *
   * @var array
   */
  private $files;

  /**
   * Icon field.
   *
   * @var \Drupal\Core\Field\FieldDefinition
   */
  private $iconField;

  /**
   * Generate media icons from SVG-files.
   *
   * @param string $path
   *   Argument provided to the command.
   * @param array $options
   *   Options.
   *
   * @command suopa_editorial:generateIcons
   * @aliases gemi
   * @options path Path to SVG files without trailing slash.
   * @usage gemi
   *   Adds all svg files as media entities from current folder.
   * @usage gemi /themes/custom/suomidigi/icons/svg --update
   *   Updates all changed svg files from given folder.
   * @usage gemi /themes/custom/suomidigi/icons/svg --clean
   *   Deletes all programmatically added icons and files.
   *
   * @return false
   *   Returns false or nothing.
   */
  public function generateIcons(
    $path = NULL,
    array $options = ['update' => FALSE, 'clean' => FALSE]
  ) {
    if (!$this->isIconEnabled()) {
      $this->output()->writeln('Couldn\'t find icon media bundle');
      return FALSE;
    }
    else {
      $icons_dir = 'public://' . $this->iconField->getSettings()['file_directory'];

      if (!file_exists($icons_dir)) {
        $this->output()->writeln('Attempting to create icons directory...');

        if (!\Drupal::service('file_system')->prepareDirectory($icons_dir, FileSystemInterface::CREATE_DIRECTORY)) {
          $this->output()->writeln('The directory %directory could not be created. To proceed with the setup, either create the directory "%directory" manually or ensure that the installer has the permissions to create it automatically.', ['%directory' => $icons_dir]);
          return FALSE;
        }

        $this->output()->writeln('Successfully created icons directory.');
      }
    }

    $path = DRUPAL_ROOT . $path;
    $files = file_scan_directory($path, '/.*.svg$/i');
    if (empty($files)) {
      $this->output()->writeln('Couldn\'t find any SVG files from ' . $path);
      return FALSE;
    }
    else {
      $this->files = $files;
    }

    if ($options['update']) {
      $this->output()->writeln('Attempting to update icons. Path: ' . $path);
      $this->createIconEntities(TRUE);
    }
    elseif ($options['clean']) {
      $this->output()->writeln('Attempting to delete icons. Path: ' . $path);
      $this->cleanUp();
    }
    else {
      $this->output()->writeln('Attempting to create icons. Path: ' . $path);
      $this->createIconEntities();
    }
  }

  /**
   * Check if icon is enabled.
   */
  private function isIconEnabled() {
    $bundles = \Drupal::service('entity_type.bundle.info')->getBundleInfo('media');

    if (array_key_exists('icon', $bundles)) {
      $fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('media', 'icon');

      foreach ($fields as $field) {
        if ($field->getType() === 'image' && $field->getSettings()['file_extensions'] === 'svg') {
          $this->iconField = $field;
          return TRUE;
        }
      }
    }

    return FALSE;
  }

  /**
   * Create icon media entities from retrieved files.
   */
  private function createIconEntities($update = FALSE) {
    if (!empty($this->files)) {
      foreach ($this->files as $file) {
        $human_readable = '';
        $alt_title = '';

        $image_data = file_get_contents($file->uri);
        $file_image = file_save_data(
          $image_data,
          'public://' . $this->iconField->getSettings()['file_directory'] . '/' . $file->filename,
          FileSystemInterface::EXISTS_REPLACE
        );

        if (!$file_image) {
          $this->output()->writeln('Couldn\'t create icon file. Please check your files directory permissions.');
          return FALSE;
        }

        $human_readable = str_replace('-', ' ', ucfirst($file->name));

        $res = preg_match('/<title>(.*)<\/title>/siU', $image_data, $title_matches);
        if ($res) {
          $title = preg_replace('/\s+/', ' ', $title_matches[1]);
          $alt_title = trim($title);
        }

        if ($update) {
          $query = \Drupal::entityQuery('media')->condition('name', $file->name);
          $media_ids = $query->execute();

          if (!empty($media_ids)) {
            $media_entities = Media::loadMultiple($media_ids);

            foreach ($media_entities as $media_image) {
              $media_image->setName($human_readable);
              $media_image->{$this->iconField->get('field_name')}->alt = (!empty($alt_title)) ? $alt_title : $human_readable;
              $media_image->save();
              $this->output()->writeln('Updated icon: ' . $file->name);
            }
          }
        }
        else {
          $media_image = Media::create([
            'bundle' => $this->iconField->get('bundle'),
            'name' => $human_readable,
            $this->iconField->get('field_name') => [
              'target_id' => $file_image->id(),
              'alt' => (!empty($alt_title)) ? $alt_title : $human_readable,
            ],
          ]);
          $media_image->save();
          $this->output()->writeln('Created icon: ' . $file->name);
        }
      }
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Create icon media entities from retrieved files.
   */
  private function cleanUp() {
    if (!empty($this->files)) {
      foreach ($this->files as $file) {
        $query = \Drupal::entityQuery('media')->condition('name', $file->name);
        $media_ids = $query->execute();

        if (!empty($media_ids)) {
          $media_entities = Media::loadMultiple($media_ids);

          foreach ($media_entities as $entity) {
            $entity->delete();
            $this->output()->writeln('Deleted icon: ' . $file->filename);
          }
        }
        else {
          $deleted = FALSE;
          $query = \Drupal::entityQuery('file')->condition('filename', $file->filename);
          $file_managed_ids = $query->execute();
          $file_entities = File::loadMultiple($file_managed_ids);

          foreach ($file_entities as $file_entity) {
            $file_entity->setTemporary();
            $deleted = TRUE;
          }

          if ($deleted) {
            $this->output()->writeln('Set managed file to be cleaned during garbage collection: ' . $file->filename);
          }
        }
      }
      return TRUE;
    }

    return FALSE;
  }

}
