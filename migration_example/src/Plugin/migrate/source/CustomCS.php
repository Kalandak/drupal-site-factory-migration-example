<?php

declare(strict_types=1);

namespace Drupal\migration_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for CS paragraphs.
 *
 * @MigrateSource(
 *   id = "custom_paragraph_cs"
 * )
 */
class CustomCS extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('paragraphs_item_field_data', 'pifd')
      ->fields('pifd', ['id', 'revision_id', 'type', 'created'])
      ->condition('type', 'content_slide')
      ->condition('status', 1);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('ID'),
      'field_slide_content' => $this->t('Slider Content'),
      'field_slide_headline' => $this->t('Slide Headline'),
      'field_slide_image' => $this->t('Slide Image'),
      'field_slide_link' => $this->t('Link'),
      'field_slide_media' => $this->t('Slide Media'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Load body field.
    $id = $row->getSourceProperty('id');
    $field_slide_content = $this->select('paragraph__field_slide_content', 'pfsc')
      ->fields('pfsc', ['field_slide_content_value', 'field_slide_content_value'])
      ->condition('entity_id', $id)
      ->execute()
      ->fetchAssoc();

    if ($field_slide_content) {
      $row->setSourceProperty('field_slide_content', $field_slide_content);
    }

    // Load values for field_adprom.
    $field_slide_headline = $this->select('paragraph__field_slide_headline', 'pfca')
      ->fields('pfca', ['field_slide_headline_value', 'field_slide_headline_format'])
      ->condition('entity_id', $id)
      ->execute()
      ->fetchAssoc();

    if ($field_slide_headline) {
      $row->setSourceProperty('field_slide_headline', $field_slide_headline);
    }

    // Load value for field_audience_notice.
    $field_slide_image = $this->select('paragraph__field_slide_image', 'pfds')
      ->fields('pfds', ['field_slide_image_target_id', 'field_slide_image_alt', 'field_slide_image_title'])
      ->condition('entity_id', $id)
      ->execute()
      ->fetchCol();

    if ($field_slide_image) {
      $row->setSourceProperty('field_slide_image', $field_slide_image);
    }

    $field_slide_link = $this->select('paragraph__field_slide_link', 'pfds')
      ->fields('pfds', ['field_slide_link_uri', 'field_slide_link_title', 'field_slide_link_options'])
      ->condition('entity_id', $id)
      ->execute()
      ->fetchAssoc();

    if ($field_slide_link) {
      $row->setSourceProperty('field_slide_link', $field_slide_link);
    }

    $query = $this->select('paragraph__field_slide_media', 'pfsm');
    $query->fields('pfsm', ['field_slide_media_target_id']);
    $query->condition('pfsm.entity_id', $id);
    $field_media_image_target_id = $query->distinct()->execute()
      ->fetchCol();

    if ($field_media_image_target_id) {
      $row->setSourceProperty('field_slide_media', $field_media_image_target_id);
    }

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type' => 'integer',
        'alias' => 'pifd',
      ],
    ];
  }

}
