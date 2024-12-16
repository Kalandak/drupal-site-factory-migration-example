<?php

namespace Drupal\migration_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for webform embed right paragraphs.
 *
 * @MigrateSource(
 *   id = "custom_webform_embed_right"
 * )
 */
class CustomWebformEmbedRight extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node__field_inner_landing_page', 'nil');
    $query->join('paragraph__field_right_column', 'pft', 'nil.field_inner_landing_page_target_id=pft.entity_id');
    $query->join('paragraph__field_url', 'pfu', 'pft.field_right_column_target_id=pfu.entity_id');
    $query->fields('pfu', ['entity_id', 'revision_id'])
      ->condition('pfu.bundle', 'webform_embed');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('ID'),
      'field_url' => $this->t('Resource Title'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $id = $row->getSourceProperty('entity_id');
    $field_url = $this->select('paragraph__field_url', 'pfu')
      ->fields('plink', ['field_url_uri', 'field_url_title', 'field_url_options'])
      ->condition('entity_id', $id)
      ->execute()
      ->fetchAssoc();

    if ($field_url) {
      $row->setSourceProperty('field_url', $field_url);
    }

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'entity_id' => [
        'type' => 'integer',
        'alias' => 'pfu',
      ],
    ];
  }

}
