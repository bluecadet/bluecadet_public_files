<?php

namespace Drupal\bluecadet_public_files\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Core\Queue\QueueWorkerBase;
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
class ScanPublicFilesDir extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   *
   * TODO: change this to use symphony/finder
   */
  public function processItem($data) {
    $dir = $data->dir;

    $queue_factory = \Drupal::service('queue');

    $dir_queue = $queue_factory->get('scan_public_files_dir');
    $file_queue = $queue_factory->get('check_public_files');

    $override_directories = array(
      'public://styles',
      'public://ctools',
      'public://private'
    );

    if (is_dir($dir) && $handle = opendir($dir)) {
      while (FALSE !== ($filename = readdir($handle))) {
        if ($filename[0] != '.') {
          $uri = "$dir/$filename";
          $uri = file_stream_wrapper_uri_normalize($uri);
          if (is_dir($uri) && !in_array($uri, $override_directories)) {
            // Register directory queue;
            $item = new \stdClass();
            $item->dir = $uri;
            $dir_queue->createItem($item);
          }
          else if (!is_dir($uri)) {
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