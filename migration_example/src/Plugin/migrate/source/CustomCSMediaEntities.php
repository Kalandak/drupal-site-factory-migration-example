<?php

declare(strict_types=1);

namespace Drupal\migration_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for CS media entities.
 *
 * @MigrateSource(
 *   id = "custom_cs_media_entities"
 * )
 */
class CustomCSMediaEntities extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('paragraph__field_slide_media', 'pfsm');
    $query->join('media__field_media_image', 'mfmi', 'pfsm.field_slide_media_target_id=mfmi.entity_id');
    $query->fields(
      'mfmi',
      ['entity_id', 'field_media_image_target_id', 'field_media_image_alt', 'field_media_image_title']
    );
    $query->condition('pfsm.bundle', 'content_slide');
    $query->distinct();
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
