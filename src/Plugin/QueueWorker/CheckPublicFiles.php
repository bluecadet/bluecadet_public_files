<?php

namespace Drupal\bluecadet_public_files\Plugin\QueueWorker;

use Drupal\Core\Database\Connection;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Check Public files on CRON run.
 *
 * @QueueWorker(
 *   id = "check_public_files",
 *   title = @Translation("Check Public Files"),
 *   cron = {"time" = 15}
 * )
 */
class CheckPublicFiles extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * Database Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database, FileSystemInterface $file_system) {
    $this->configuration = $configuration;
    $this->pluginId = $plugin_id;
    $this->pluginDefinition = $plugin_definition;
    $this->database = $database;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
          $configuration,
          $plugin_id,
          $plugin_definition,
          $container->get('database'),
          $container->get('file_system')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $uri = $data->uri;
    $filename = $data->filename;

    $query = $this->database->select('file_managed', 'f');
    $query->fields('f', ['fid']);
    $query->condition('uri', $uri);
    $r = $query->execute()->fetchAll();

    if (empty($r)) {
      // Create DB log.
      $this->database->merge('bluecadet_public_files')
        ->key(['uri' => $uri])
        ->fields(
                [
                  'filename' => $filename,
                  'filesize' => filesize($this->fileSystem->realpath($uri)),
                  'timestamp' => time(),
                ]
            )
        ->execute();
    }
  }

}
