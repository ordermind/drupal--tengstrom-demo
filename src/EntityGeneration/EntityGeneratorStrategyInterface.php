<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\EntityGeneration;

interface EntityGeneratorStrategyInterface {

  public function generateEntities(EntityGenerationOptions $options): void;

  public function deleteExistingEntities(string $entityTypeId): void;

}
