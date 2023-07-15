<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\Plugin\DevelGenerate;

use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\devel_generate\DevelGenerateBase;
use Drupal\tengstrom_demo\Entity\TengstromDemoContent;
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
 *     "num" = 50,
 *     "kill" = FALSE
 *   }
 * )
 */
class TengstromDemoDevelGenerate extends DevelGenerateBase implements ContainerFactoryPluginInterface {
  protected const ENTITY_TYPE = 'tengstrom_demo_content';

  protected EntityTypeBundleInfoInterface $bundleInfo;
  protected AccountProxyInterface $currentUser;

  public function __construct(
    array $configuration, 
    string $plugin_id, 
    array $plugin_definition, 
    EntityTypeBundleInfoInterface $bundleInfo,
    AccountProxyInterface $currentUser
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->bundleInfo = $bundleInfo;
    $this->currentUser = $currentUser;
  }
  
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.bundle.info'),
      $container->get('current_user')
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

    $form['kill'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete all entities before generating new ones.'),
      '#default_value' => $this->getSetting('kill'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function generateElements(array $values): void {
    $num = (int) $values['num'];
    $kill = (bool) $values['kill'];

    if ($kill) {
      $this->deleteAllExistingEntities();
    }

    $this->createEntities($num);
  }
    
  /**
   * {@inheritdoc}
   */
  public function validateDrushParams(array $args, array $options = []): array {
    $values = [
      'num' => $options['num'],
      'kill' => $options['kill'],
    ];
    return $values;
  }

  protected function deleteAllExistingEntities(): void {
    $chunkSize = 100;
    $skip = 0;

    $entityQuery = \Drupal::entityQuery(static::ENTITY_TYPE)
      ->accessCheck(FALSE);

    while(
      $contentIds = $entityQuery
        ->range($skip, $chunkSize)
        ->execute()
    ) {
      foreach($contentIds as $contentId) {
        $content = TengstromDemoContent::load($contentId);
        if(!$content) {
          continue;
        }

        $content->delete();
      }

      $skip += $chunkSize;

      usleep(5000);
    }

    $this->setMessage($this->t('Old entities have been deleted.'));
  }

  protected function createEntities(int $num): void {
    if($num < 0) {
      throw new InvalidArgumentException('The "num" parameter must be a positive integer');
    }

    $storage = $this->getEntityTypeManager()->getStorage(static::ENTITY_TYPE);
    $bundles = $this->bundleInfo->getBundleInfo('tengstrom_demo_content');
    $bundleNames = array_keys($bundles);

    $uid = $this->currentUser->id();

    $chunkSize = 100;

    for($i = 0, $imax = $num; $i < $imax; $i++) {
      $baseData = [
        'bundle' => $bundleNames[rand(0, 1)],
        'uid'     => $uid,
        'label'    => 'Demo Content #' . $i + 1,
        'status'  => 1,
        'created' => \Drupal::time()->getRequestTime(),
      ];

      $entity = $storage->create($baseData);
      $this->populateFields($entity);
      $entity->save();

      if($i % $chunkSize === 0) {
        usleep(5000);
      }
    }

    $this->setMessage($this->t('@num_entities created.', [
      '@num_entities' => $this->formatPlural($num, '1 entity', '@count entities'),
    ]));
  }
}