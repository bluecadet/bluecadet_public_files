<?php

namespace Drupal\bluecadet_public_files\Controller;

use Drupal\bluecadet_public_files\DatabaseTrait;
use Drupal\bluecadet_public_files\FormBuilderTrait;
use Drupal\bluecadet_public_files\QueueTrait;
use Drupal\bluecadet_public_files\Form\ResetPublicFiles;
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller to print out the admin pages.
 */
class PublicFilesReport extends ControllerBase {

  use DatabaseTrait;
  use FormBuilderTrait;
  use QueueTrait;

  /**
   * View the report of untracked files.
   */
  public function viewReport() {

    $queue_factory = $this->getQueueFactory();
    $dir_queue = $queue_factory->get('scan_public_files_dir');
    $file_queue = $queue_factory->get('check_public_files');

    $build = [
      'status' => '',
      'reset_form' => $this->getFormBuilder()->getForm(ResetPublicFiles::class),
      'table' => [
        '#markup' => '',
      ],
    ];

    $db = $this->getDatabase();
    $query = $db->select('bluecadet_public_files', 'pf');
    $query->addExpression('SUM(filesize)', 'totalFileSize');
    $r = $query->execute()->fetch();

    $db = $this->getDatabase();
    $query = $db->select('bluecadet_public_files', 'pf');
    $num_rows = $query->countQuery()->execute()->fetchField();

    $build['status'] = [
      [
        '#markup' => 'Num of Directories in queue: ' . number_format($dir_queue->numberOfItems()),
      ],
      [
        '#markup' => '<br />Num of Files in queue: ' . number_format($file_queue->numberOfItems()) . '',
      ],
      [
        '#markup' => '<br />Total Filesize of unused files: ' . $this->formatBytes($r->totalFileSize),
      ],
      [
        '#markup' => '<br />Total number of files: ' . number_format($num_rows) . '<br /><br />',
      ],
    ];

    // Build Table.
    $header = [
      ['data' => 'ID', 'field' => 'pfrid', 'sort' => 'ASC'],
      ['data' => 'URI', 'field' => 'uri', 'sort' => 'ASC'],
      ['data' => 'Filename', 'field' => 'filename', 'sort' => 'ASC'],
      ['data' => 'Filesize', 'field' => 'filesize', 'sort' => 'DESC'],
      ['data' => 'Timestamp', 'field' => 'timestamp', 'sort' => 'ASC'],
      ['data' => 'Actions'],
    ];

    $db = $this->getDatabase();
    $query = $db->select('bluecadet_public_files', 'pf');
    $query->fields('pf');
    // The actual action of sorting the rows is here.
    $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->orderByHeader($header);
    // Limit the rows to 20 for each page.
    $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit(20);
    $result = $pager->execute();

    $rows = [];
    foreach ($result as $row) {
      $rows[] = [
        'data' => [
          'pfrid' => $row->pfrid,
          'uri' => $row->uri,
          'filename' => $row->filename,
          'filesize' => number_format($row->filesize) . ' (' . $this->formatBytes($row->filesize) . ')',
          'timestamp' => $row->timestamp,
          'action' => "",
        ],
      ];
    }

    $build['table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => [
        '#markup' => 'There is no content at this moment.',
      ],
    ];

    $build['pager'] = [
      '#type' => 'pager',
    ];

    return $build;
  }

  /**
   * Format bytes.
   */
  protected function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives.
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
  }

}
