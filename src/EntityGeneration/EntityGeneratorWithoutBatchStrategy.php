<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\EntityGeneration;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class EntityGeneratorWithoutBatchStrategy implements EntityGeneratorStrategyInterface {
  use StringTranslationTrait;

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
    if ($options->isDeleteEntitiesBeforeCreation()) {
      $this->deleteExistingEntities($options->getEntityTypeId());
    }

    $this->createNewEntities($options);
  }

  protected function deleteExistingEntities(string $entityTypeId): void {
    $this->entityDeleter->deleteAllEntitiesOfType($entityTypeId);

    $this->messenger->addStatus($this->t('Old entities have been deleted.'));
  }

  protected function createNewEntities(EntityGenerationOptions $options): void {
    $this->entityGenerator->generateEntities($options);

    $this->messenger->addStatus($this->t('@num_entities created.', [
      '@num_entities' => $this->formatPlural($options->getNumberOfEntities(), '1 entity', '@count entities'),
    ]));
  }

}
