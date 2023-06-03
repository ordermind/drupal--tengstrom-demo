<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a demo content entity type.
 */
interface TengstromDemoContentInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {
}
