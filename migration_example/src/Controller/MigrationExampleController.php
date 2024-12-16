<?php

declare(strict_types=1);

namespace Drupal\migration_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

/**
 * Returns responses for Specialty profile paragraphs migration routes.
 */
class MigrationExampleController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    Database::setActiveConnection('migrate');
    $connection = Database::getConnection('migrate');
    $query = $connection->select('node__field_resource_file', 'nfrf');
    $query->join('file_managed', 'fm', 'nfrf.field_resource_file_target_id=fm.fid');
    $query->fields('nfrf', ['entity_id', 'field_resource_file_target_id']);
    $query->fields('fm', ['filename', 'fid']);
    $query->distinct()->execute()->fetchAllAssoc('fid');
    Database::setActiveConnection();

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
