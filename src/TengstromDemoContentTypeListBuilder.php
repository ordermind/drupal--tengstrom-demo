<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of demo content type entities.
 *
 * @see \Drupal\tengstrom_demo\Entity\TengstromDemoContentType
 */
class TengstromDemoContentTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Label');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['title'] = [
      'data' => $entity->label(),
      'class' => ['menu-label'],
    ];

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    $build['table']['#empty'] = $this->t(
      'No demo content types available. <a href=":link">Add demo content type</a>.',
      [':link' => Url::fromRoute('entity.tengstrom_demo_content_type.add_form')->toString()]
    );

    return $build;
  }

}
