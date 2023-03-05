<?php

namespace Drupal\bluecadet_public_files\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Scan Public directories on CRON run.
 *
 * @QueueWorker(
 *   id = "scan_public_files_dir",
 *   title = @Translation("Scan Public Files Dir"),
 *   cron = {"time" = 15}
 * )
 */
class ScanPublicFilesDir extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The queue factory.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * The stream Wrapper Manage.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManager
   */
  protected $streamWrapperManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, QueueFactory $queue_factory, StreamWrapperManager $stream_wrapper_manager) {
    $this->configuration = $configuration;
    $this->pluginId = $plugin_id;
    $this->pluginDefinition = $plugin_definition;
    $this->queueFactory = $queue_factory;
    $this->streamWrapperManager = $stream_wrapper_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
          $configuration,
          $plugin_id,
          $plugin_definition,
          $container->get('queue'),
          $container->get('stream_wrapper_manager')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $dir = $data->dir;

    $dir_queue = $this->queueFactory->get('scan_public_files_dir');
    $file_queue = $this->queueFactory->get('check_public_files');

    $override_directories = [
      'public://styles',
      'public://ctools',
      'public://private',
    ];

    if (is_dir($dir) && $handle = opendir($dir)) {
      while (FALSE !== ($filename = readdir($handle))) {
        if ($filename[0] != '.') {
          $uri = "$dir/$filename";
          $uri = $this->streamWrapperManager->normalizeUri($uri);
          if (is_dir($uri) && !in_array($uri, $override_directories)) {
            // Register directory queue;.
            $item = new \stdClass();
            $item->dir = $uri;
            $dir_queue->createItem($item);
          }
          elseif (!is_dir($uri)) {
            // Register File.
            $item = new \stdClass();
            $item->uri = $uri;
            $item->filename = $filename;
            $file_queue->createItem($item);
          }
        }
      }

      closedir($handle);
    }
  }

}
