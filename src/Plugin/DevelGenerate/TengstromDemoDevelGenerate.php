<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\Plugin\DevelGenerate;

use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\devel_generate\DevelGenerateBase;
use Drupal\tengstrom_demo\Entity\TengstromDemoContent;
use Drupal\tengstrom_demo\EntityGeneration\EntityGenerationOptions;
use Drupal\tengstrom_demo\EntityGeneration\EntityGeneratorStrategyInterface;
use Drupal\tengstrom_demo\EntityGeneration\EntityGeneratorWithBatchStrategy;
use Drupal\tengstrom_demo\EntityGeneration\EntityGeneratorWithoutBatchStrategy;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a DevelGenerate plugin.
 *
 * @DevelGenerate(
 *   id = "devel_generate_tengstrom_demo",
 *   label = "Tengstrom Demo Content",
 *   description = "Generate a given number of tengstrom demo content.",
 *   url = "devel_generate_tengstrom_demo",
 *   permission = "administer devel_generate",
 *   settings = {
 *     "num" = 100,
 *     "delete_existing" = TRUE,
 *     "batch_minimum_limit" = 50
 *   }
 * )
 */
class TengstromDemoDevelGenerate extends DevelGenerateBase implements ContainerFactoryPluginInterface {
  protected const ENTITY_TYPE = 'tengstrom_demo_content';

  protected EntityTypeBundleInfoInterface $bundleInfo;
  protected EntityGeneratorWithBatchStrategy $entityGeneratorBatchStrategy;
  protected EntityGeneratorWithoutBatchStrategy $entityGeneratorNoBatchStrategy;

  public function __construct(
    array $configuration, 
    string $plugin_id, 
    array $plugin_definition, 
    EntityTypeBundleInfoInterface $bundleInfo,
    EntityGeneratorWithBatchStrategy $entityGeneratorBatchStrategy,
    EntityGeneratorWithoutBatchStrategy $entityGeneratorNoBatchStrategy
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->bundleInfo = $bundleInfo;
    $this->entityGeneratorBatchStrategy = $entityGeneratorBatchStrategy;
    $this->entityGeneratorNoBatchStrategy = $entityGeneratorNoBatchStrategy;
  }
  
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.bundle.info'),
      $container->get('tengstrom_demo_entity_generator_batch_strategy'),
      $container->get('tengstrom_demo_entity_generator_nobatch_strategy'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form['num'] = [
      '#type' => 'textfield',
      '#title' => $this->t('How many entities would you like to generate?'),
      '#default_value' => $this->getSetting('num'),
      '#size' => 10,
    ];

    $form['delete_existing'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete all entities before generating new ones.'),
      '#default_value' => $this->getSetting('delete_existing'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function generateElements(array $values): void {
    $num = (int) $values['num'];
    $deleteExisting = (bool) $values['delete_existing'];
    $bundles = $this->bundleInfo->getBundleInfo(static::ENTITY_TYPE);
    $bundleNames = array_keys($bundles);
    
    $generationOptions = new EntityGenerationOptions(
      static::ENTITY_TYPE,
      'Demo Content #@num',
      $bundleNames,
      $num,
      $deleteExisting
    );

    $strategy = $this->getStrategy($num);

    $strategy->generateEntities($generationOptions);
  }
    
  /**
   * {@inheritdoc}
   */
  public function validateDrushParams(array $args, array $options = []): array {
    $values = [
      'num' => $options['num'],
      'deleteExisting' => $options['deleteExisting'],
    ];
    return $values;
  }

  protected function getStrategy(int $num): EntityGeneratorStrategyInterface {
    // if($num > $this->getSetting('batch_minimum_limit')) {
    //   return $this->entityGeneratorBatchStrategy;
    // }

    return $this->entityGeneratorNoBatchStrategy;
  }
}