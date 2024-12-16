<?php

namespace Drupal\migration_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for Single Featured Resource paragraphs.
 *
 * @MigrateSource(
 *   id = "custom_paragraph_sfr"
 * )
 */
class CustomSFR extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node__field_inner_landing_page', 'nil');
    $query->join('paragraph__field_resource_sections', 'pft', 'nil.field_inner_landing_page_target_id=pft.entity_id');
    $query->join('paragraph__field_resource_title', 'pfrt', 'pft.field_resource_sections_target_id=pfrt.entity_id');
    $query->fields('pfrt', ['entity_id', 'revision_id'])
      ->condition('pfrt.bundle', 'single_featured_resource');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('ID'),
      'field_resource_title' => $this->t('Resource Title'),
      'field_link' => $this->t('Call to Action Button Link'),
      'field_short_description' => $this->t('Description'),
      'field_media_image' => $this->t('Image'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $id = $row->getSourceProperty('entity_id');
    $field_resource_title = $this->select('paragraph__field_resource_title', 'pfrt')
      ->fields('pfrt', ['field_resource_title_value', 'field_resource_title_format'])
      ->condition('entity_id', $id)
      ->execute()
      ->fetchAssoc();

    if ($field_resource_title) {
      $row->setSourceProperty('field_resource_title', $field_resource_title);
    }

    $field_link = $this->select('paragraph__field_link', 'plink')
      ->fields('plink', ['field_link_uri', 'field_link_title', 'field_link_options'])
      ->condition('entity_id', $id)
      ->execute()
      ->fetchAssoc();

    if ($field_link) {
      $row->setSourceProperty('field_link', $field_link);
    }

    $field_short_description = $this->select('paragraph__field_short_description', 'psfd')
      ->fields('psfd', ['field_short_description_value', 'field_short_description_format'])
      ->condition('entity_id', $id)
      ->execute()
      ->fetchAssoc();

    if ($field_short_description) {
      $row->setSourceProperty('field_short_description', $field_short_description);
    }

    $query = $this->select('paragraph__field_media_image', 'pfmi');
    $query->fields('pfmi', ['field_media_image_target_id']);
    $query->condition('pfmi.entity_id', $id);
    $field_single_featured_resource_image = $query->distinct()->execute()->fetchCol();

    if ($field_single_featured_resource_image) {
      $row->setSourceProperty('field_media_component', $field_single_featured_resource_image[0]);
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
        'alias' => 'pfrt',
      ],
    ];
  }

}
