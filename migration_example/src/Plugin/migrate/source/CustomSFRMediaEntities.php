<?php

namespace Drupal\migration_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for SFR media entities.
 *
 * @MigrateSource(
 *   id = "custom_sfr_media_entities"
 * )
 */
class CustomSFRMediaEntities extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('paragraph__field_media_image', 'pfmi');
    $query->join('media__field_media_image', 'mfmi', 'pfmi.field_media_image_target_id=mfmi.entity_id');
    $query->fields(
      'mfmi',
      ['entity_id', 'field_media_image_target_id', 'field_media_image_alt', 'field_media_image_title']
    );
    $query->condition('pfmi.bundle', 'single_featured_resource');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'entity_id' => $this->t('Media ID'),
      'field_media_image_target_id' => $this->t('Image Taget Id'),
      'field_media_image_alt' => $this->t('Image Alt'),
      'field_media_image_title' => $this->t('Image Title'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'entity_id' => [
        'type' => 'integer',
        'alias' => 'mfmi',
      ],
    ];
  }

}
