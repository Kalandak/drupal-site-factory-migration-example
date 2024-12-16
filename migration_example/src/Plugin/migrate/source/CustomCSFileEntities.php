<?php

declare(strict_types=1);

namespace Drupal\migration_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for CS file entities.
 *
 * @MigrateSource(
 *   id = "custom_cs_file_entities"
 * )
 */
class CustomCSFileEntities extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('paragraph__field_slide_media', 'pfsm');
    $query->join('media__field_media_image', 'mfmi', 'pfsm.field_slide_media_target_id=mfmi.entity_id');
    $query->fields('mfmi', ['field_media_image_target_id']);
    $query->condition('pfsm.bundle', 'content_slide');
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
    $fid = $row->getSourceProperty('field_media_image_target_id');

    $file = $this->select('file_managed', 'f')
      ->fields('f', ['fid', 'uid', 'filename', 'uri'])
      ->condition('fid', $fid)
      ->execute()
      ->fetchAssoc();

    if ($file) {
      $row->setSourceProperty('fid', $file['fid']);
      $row->setSourceProperty('uri', str_replace('public:/', 'https://mykidneyjourney.com/sites/g/files/ebysai3126/files', $file['uri']));
      $row->setSourceProperty('uid', $file['uid']);
      $row->setSourceProperty('filename', $file['filename']);
    }

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'field_media_image_target_id' => [
        'type' => 'integer',
        'alias' => 'mfmi',
      ],
    ];
  }

}
