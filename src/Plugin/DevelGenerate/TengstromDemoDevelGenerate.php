<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\Plugin\DevelGenerate;

use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\devel_generate_custom_entities\Factory\EntityGeneratorStrategyFactory;
use Drupal\devel_generate_custom_entities\Plugin\DevelGenerate\AbstractDevelGeneratePlugin;
use Drupal\devel_generate_custom_entities\Strategy\EntityGeneratorWithBatchStrategy;
use Drupal\devel_generate_custom_entities\Strategy\EntityGeneratorWithoutBatchStrategy;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a DevelGenerate plugin.
 *
 * @DevelGenerate(
 *   id = "tengstrom_demo_devel_generate",
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
  protected EntityTypeBundleInfoInterface $bundleInfo;
  protected EntityGeneratorStrategyFactory $strategyFactory;
  protected AccountProxyInterface $currentUser;

  public function __construct(
    array $configuration, 
    string $plugin_id, 
    array $plugin_definition,
    EntityGeneratorWithBatchStrategy $withBatchStrategy,
    EntityGeneratorWithoutBatchStrategy $withoutBatchStrategy,
    AccountProxyInterface $currentUser,
    EntityTypeBundleInfoInterface $bundleInfo
  ) {
    parent::__construct(
      $configuration, 
      $plugin_id, 
      $plugin_definition,
      $withBatchStrategy,
      $withoutBatchStrategy,
      $currentUser
    );

    $this->bundleInfo = $bundleInfo;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('devel_generate_custom_entities.strategy_with_batch'),
      $container->get('devel_generate_custom_entities.strategy_without_batch'),
      $container->get('current_user'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritDoc}
   */
  protected function getEntityTypeId(): string {
    return 'tengstrom_demo_content';
  }

  /**
   * {@inheritDoc}
   */
  protected function getBundleNames(): array {
    $bundles = $this->bundleInfo->getBundleInfo($this->getEntityTypeId());
    
    return array_keys($bundles);
  }

  /**
   * {@inheritDoc}
   */
  protected function getLabelPattern(): string {
    return 'Demo Content #@num';
  }
}