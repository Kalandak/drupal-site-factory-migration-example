<?php

namespace Drupal\migration_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for SMT file entities.
 *
 * @MigrateSource(
 *   id = "custom_smt_file_entities"
 * )
 */
class CustomSMTFileEntities extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('paragraph__field_small_tile_image', 'pfsti');
    $query->join('media__field_media_image', 'mfmi', 'pfsti.field_small_tile_image_target_id=mfmi.entity_id');
    $query->join('file_managed', 'fm', 'mfmi.field_media_image_target_id=fm.fid');
    $query->fields('fm', ['fid', 'uid', 'filename', 'uri']);
    $query->condition('pfsti.bundle', 'small_tile');
    $query->distinct();
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'fid' => $this->t('File ID'),
      'filename' => $this->t('Filename'),
      'uid' => $this->t('User ID'),
      'uri' => $this->t('Uri'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Load body field.
    $uri = $row->getSourceProperty('uri');

    if ($uri) {
      $row->setSourceProperty('uri', str_replace('public:/', 'https://mykidneyjourney.com/sites/g/files/ebysai3126/files', $uri));
    }

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'fid' => [
        'type' => 'integer',
        'alias' => 'fm',
      ],
    ];
  }

}
