<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\EntityGeneration;

class EntityGeneratorWithoutBatchStrategy implements EntityGeneratorStrategyInterface {

  protected EntityGenerator $entityGenerator;
  protected EntityDeleter $entityDeleter;

  public function __construct(EntityGenerator $entityGenerator, EntityDeleter $entityDeleter) {
    $this->entityGenerator = $entityGenerator;
    $this->entityDeleter = $entityDeleter;
  }

  public function generateEntities(EntityGenerationOptions $options): void {
    $this->entityGenerator->generateEntities($options);
  }

  public function deleteExistingEntities(string $entityTypeId): void {
    $this->entityDeleter->deleteAllEntitiesOfType($entityTypeId);
  }

}
