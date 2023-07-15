<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\EntityGeneration;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\devel_generate\DevelGenerateBase;

class EntityGenerator {
  protected EntityTypeManagerInterface $entityTypeManager;
  protected AccountProxyInterface $currentUser;

  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    AccountProxyInterface $currentUser
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $currentUser;
  }

  public function generateEntities(EntityGenerationOptions $options): void {
    if ($options->getNumberOfEntities() < 0) {
      throw new \InvalidArgumentException('The "num" parameter must be a positive integer');
    }

    $storage = $this->entityTypeManager->getStorage($options->getEntityTypeId());
    $uid = $uid ?? $this->currentUser->id();

    $chunkSize = 100;

    for ($i = 0, $imax = $options->getNumberOfEntities(); $i < $imax; $i++) {
      $baseData = $options->getBaseData() + [
        'bundle' => $options->getBundleNames()[rand(0, count($options->getBundleNames()) - 1)],
        'uid'     => $uid,
        'label'    => str_replace('@num', (string) ($i + 1), $options->getLabelPattern()),
        'status'  => 1,
        'created' => \Drupal::time()->getRequestTime(),
      ];

      $entity = $storage->create($baseData);
      DevelGenerateBase::populateFields($entity);
      $entity->save();

      if ($i % $chunkSize === 0) {
        usleep(5000);
      }
    }
  }

}
