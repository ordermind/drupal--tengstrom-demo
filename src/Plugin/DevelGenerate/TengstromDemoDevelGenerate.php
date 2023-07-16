<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\Plugin\DevelGenerate;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\devel_generate_custom_entities\Plugin\DevelGenerate\AbstractDevelGeneratePlugin;

/**
 * Provides a DevelGenerate plugin.
 *
 * @DevelGenerate(
 *   id = "tengstrom_demo_content",
 *   label = "Tengstrom Demo Content",
 *   description = "Generate a given number of tengstrom demo content.",
 *   url = "tengstrom_demo_devel_generate",
 *   permission = "administer devel_generate",
 *   settings = {
 *     "num" = 100,
 *     "delete_existing" = TRUE,
 *     "batch_minimum_limit" = 50
 *   }
 * )
 */
class TengstromDemoDevelGenerate extends AbstractDevelGeneratePlugin implements ContainerFactoryPluginInterface {
  /**
   * {@inheritDoc}
   */
  protected function getEntityTypeId(): string {
    return 'tengstrom_demo_content';
  }

  /**
   * {@inheritDoc}
   */
  protected function getLabelPattern(): string {
    return 'Demo Content #@num';
  }
}