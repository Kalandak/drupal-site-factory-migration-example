<?php

declare(strict_types=1);

namespace Drupal\migration_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Migration source plugin for SMT media entities.
 *
 * @MigrateSource(
 *   id = "custom_smt_media_entities"
 * )
 */
class CustomSMTMediaEntities extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('paragraph__field_small_tile_image', 'pfsti');
    $query->join('media__field_media_image', 'mfmi',
      'pfsti.field_small_tile_image_target_id=mfmi.entity_id');
    $query->fields('mfmi', [
      'entity_id',
      'field_media_image_target_id',
      'field_media_image_alt',
      'field_media_image_title',
    ]);
    $query->condition('pfsti.bundle', 'small_tile');
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
