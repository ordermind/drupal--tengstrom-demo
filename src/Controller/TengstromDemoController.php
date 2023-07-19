<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\views\Views;

class TengstromDemoController extends ControllerBase {

  public function index() {
    $view = Views::getView('demo_content');
    if (!is_object($view)) {
      throw new \RuntimeException('The view demo_content could not be loaded');
    }

    $view->setDisplay('embed_1');
    $view->preExecute();
    $view->execute();

    return [
      'view' => $view->buildRenderable('embed_1'),
      'custom_table' => $this->createCustomAccordionTable(),
    ];
  }

  /**
   * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
   */
  protected function createCustomAccordionTable(): array {
    return [
      '#type' => 'accordion_table',
      '#caption' => $this->t('Custom accordion table'),
      '#header' => [
        'col_1' => $this->t('Col 1'),
        'col_2' => $this->t('Col 2'),
        'col_3' => $this->t('Col 3'),
      ],
      '#rows' => [
        ['col_1' => 'Row 1 Col 1', 'col_2' => 'Row 1 Col 2', 'col_3' => 'Row 1 Col 3'],
        ['col_1' => 'Row 2 Col 1', 'col_2' => 'Row 2 Col 2', 'col_3' => 'Row 2 Col 3'],
        ['col_1' => 'Row 3 Col 1', 'col_2' => 'Row 3 Col 2', 'col_3' => 'Row 3 Col 3'],
      ],
      '#attributes' => [
        'class' => ['zebra'],
      ],
    ];
  }

}
