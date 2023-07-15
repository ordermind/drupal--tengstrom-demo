<?php

/**
 * @file
 * Provides common batch functions for every DevelGeneratePlugin.
 */

use Drupal\tengstrom_demo\EntityGeneration\EntityGeneratorWithBatchStrategy;

/**
 * Calls the correct method responsible for handling a given batch operation.
 */
function tengstrom_demo_operation(EntityGeneratorWithBatchStrategy $object, $method, $vars, &$context) {
  return $object->$method($vars, $context);
}

/**
 * Standard finish batch function.
 */
function tengstrom_demo_batch_finished(bool $success, array $results, array $operations) {
  /** @var \Drupal\tengstrom_demo\EntityGeneration\EntityGeneratorWithBatchStrategy $service */
  $service = \Drupal::service('tengstrom_demo_entity_generator_batch_strategy');

  $service->printBatchFinishedMessage($success, $results['currentNumber']);
}
