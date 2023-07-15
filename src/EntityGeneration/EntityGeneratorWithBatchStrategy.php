<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\EntityGeneration;

use Drupal\Core\Messenger\MessengerInterface;

class EntityGeneratorWithBatchStrategy implements EntityGeneratorStrategyInterface {

  protected bool $drushBatch;

  protected MessengerInterface $messenger;
  protected EntityGenerator $entityGenerator;
  protected EntityDeleter $entityDeleter;

  public function __construct(
    MessengerInterface $messenger,
    EntityGenerator $entityGenerator,
    EntityDeleter $entityDeleter,
  ) {
    $this->messenger = $messenger;
    $this->entityGenerator = $entityGenerator;
    $this->entityDeleter = $entityDeleter;
  }

  public function generateEntities(EntityGenerationOptions $options): void {
    // If it is drushBatch then this operation is already run in the
    // self::validateDrushParams().
    if (!$this->drushBatch) {
      // Setup the batch operations and save the variables.
      $operations[] = ['devel_generate_operation',
        [$this, 'batchContentPreNode', $values],
      ];
    }

    // Add the deleteExisting operation.
    if ($values['deleteExisting']) {
      $operations[] = ['devel_generate_operation',
        [$this, 'batchContentdeleteExisting', $values],
      ];
    }

    // Add the operations to create the nodes.
    for ($num = 0; $num < $values['num']; $num++) {
      $operations[] = ['devel_generate_operation',
        [$this, 'batchContentAddNode', $values],
      ];
    }

    // Set the batch.
    $batch = [
      'title' => $this->t('Generating Content'),
      'operations' => $operations,
      'finished' => 'devel_generate_batch_finished',
      'file' => \Drupal::service('extension.path.resolver')->getPath('module', 'devel_generate') . '/devel_generate.batch.inc',
    ];

    batch_set($batch);
    if ($this->drushBatch) {
      drush_backend_batch_process();
    }
  }

  public function deleteExistingEntities(string $entityTypeId): void {}

  /**
   * Batch wrapper for calling ContentPreNode.
   */
  public function batchContentPreNode($vars, &$context) {
    $context['results'] = $vars;
    $context['results']['num'] = 0;
    $context['results']['num_translations'] = 0;
    $this->develGenerateContentPreNode($context['results']);
  }

}
