<?php

declare(strict_types=1);

namespace Drupal\migration_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Source plugin for CSW paragraphs.
 *
 * @MigrateSource(
 *   id = "custom_paragraph_csw"
 * )
 */
class CustomCSW extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node__field_inner_landing_page', 'nil');
    $query->join('paragraph__field_slider_content_slide', 'pfscs', 'nil.field_inner_landing_page_target_id=pfscs.entity_id');
    $query->fields('pfscs', ['field_slider_content_slide_target_id']);
    $query->fields('nil', ['field_inner_landing_page_target_id']);
    $query->condition('pfscs.bundle', 'content_slider');
    $query->distinct();

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'field_inner_landing_page_target_id' => $this->t('Entity Id'),
      'field_slider_content_slide_target_id' => $this->t('Content Slide Target Id'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'field_inner_landing_page_target_id' => [
        'type' => 'integer',
        'alias' => 'nil',
      ],
      'field_slider_content_slide_target_id' => [
        'type' => 'integer',
        'alias' => 'pfscs',
      ],
    ];
  }

}
