<?php

namespace Drupal\bluecadet_public_files\Controller;

use Drupal\Core\Controller\ControllerBase;

class PublicFilesReport extends ControllerBase {
  public function viewReport() {

    $queue_factory = \Drupal::service('queue');
    $dir_queue = $queue_factory->get('scan_public_files_dir');
    $file_queue = $queue_factory->get('check_public_files');


    // $item = new \stdClass();
    // $item->dir = 'public://';
    // $dir_queue->createItem($item);


    $build = array(
      'status' => '',
      'reset_form' => \Drupal::formBuilder()->getForm(\Drupal\bluecadet_public_files\Form\ResetPublicFiles::class),
      'table' => array(
        '#markup' => '',
      ),
    );

    //
    $db = \Drupal\Core\Database\Database::getConnection();
    $query = $db->select('bluecadet_public_files', 'pf');
    $query->addExpression('SUM(filesize)', 'totalFileSize');
    $r = $query->execute()->fetch();

    $build['status'] = [
      [
        '#markup' => 'Num of Directories in queue: ' . $dir_queue->numberOfItems(),
      ],
      [
        '#markup' => '<br />Num of Files in queue: ' . $file_queue->numberOfItems() . '',
      ],
      [
        '#markup' => '<br />Total Filesize of unused files: ' . $this->formatBytes($r->totalFileSize) . '<br /><br />',
      ],
    ];


    // Build Table
    $header = array(
      array('data' => 'ID',         'field' => 'pfrid',       'sort' => 'ASC'),
      array('data' => 'URI',        'field' => 'uri',         'sort' => 'ASC'),
      array('data' => 'Filename',   'field' => 'filename',    'sort' => 'ASC'),
      array('data' => 'Filesize',   'field' => 'filesize',    'sort' => 'DESC'),
      array('data' => 'Timestamp',  'field' => 'timestamp',   'sort' => 'ASC'),
    );

    $db = \Drupal::database();
    $query = $db->select('bluecadet_public_files','pf');
    $query->fields('pf');
    // The actual action of sorting the rows is here.
    $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
                    ->orderByHeader($header);
    // Limit the rows to 20 for each page.
    $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
              ->limit(20);
    $result = $pager->execute();

    $rows = [];
    foreach($result as $row) {
      $rows[] = ['data' => [
        'pfrid' => $row->pfrid,
        'uri' => $row->uri,
        'filename' => $row->filename,
        'filesize' => $row->filesize . ' (' . $this->formatBytes($row->filesize) . ')',
        'timestamp' => $row->timestamp,
      ]];
    }

    $build['table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => [
        '#markup' => 'There is no content at this moment.'
      ]
    ];

    $build['pager'] = [
      '#type' => 'pager'
    ];

    return $build;
  }

  protected function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
  }
}