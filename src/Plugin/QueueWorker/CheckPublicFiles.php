<?php

namespace Drupal\bluecadet_public_files\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
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
class CheckPublicFiles extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   *
   * TODO: change this to use symphony/finder
   */
  public function processItem($data) {
    $uri = $data->uri;
    $filename = $data->filename;

    // $q = db_select('file_managed', 'fm');
    // $q->fields('fm', array('fid'));
    // $q->condition('uri', $uri);
    // $r = $q->execute()->fetchAssoc();

    $db = \Drupal\Core\Database\Database::getConnection();
    $query = $db->select('file_managed', 'f');
    $query->fields('f', ['fid']);
    $query->condition('uri', $uri);
    $r = $query->execute()->fetchAll();

    if (empty($r)) {
      // Create DB log.
      // $record = array(
      //   'uri' => $uri,
      //   'filename' => $filename,
      //   'filesize' => filesize(drupal_realpath($uri)),
      //   'timestamp' => time(),
      // );
      // drupal_write_record('nasm_files_report', $record);

      \Drupal::database()->merge('bluecadet_public_files')
        ->key(['uri' => $uri])
        ->fields([
            'filename' => $filename,
            'filesize' => filesize(drupal_realpath($uri)),
            'timestamp' => time(),
          ])
        ->execute();
    }
  }
}