<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the demo content entity edit forms.
 */
class TengstromDemoContentForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New demo content %label has been created.', $message_arguments));
        $this->logger('tengstrom_demo')->notice('Created new demo content %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The demo content %label has been updated.', $message_arguments));
        $this->logger('tengstrom_demo')->notice('Updated demo content %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.tengstrom_demo_content.canonical', ['tengstrom_demo_content' => $entity->id()]);

    return $result;
  }

}
