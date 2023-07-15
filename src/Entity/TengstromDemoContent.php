<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\tengstrom_demo\TengstromDemoContentInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the demo content entity class.
 *
 * @ContentEntityType(
 *   id = "tengstrom_demo_content",
 *   label = @Translation("Demo Content"),
 *   label_collection = @Translation("Demo Content Items"),
 *   label_singular = @Translation("demo content"),
 *   label_plural = @Translation("demo content items"),
 *   label_count = @PluralTranslation(
 *     singular = "@count demo content item",
 *     plural = "@count demo content items",
 *   ),
 *   bundle_label = @Translation("Demo Content type"),
 *   handlers = {
 *     "list_builder" = "Drupal\tengstrom_demo\TengstromDemoContentListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\tengstrom_demo\TengstromDemoContentAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\tengstrom_demo\Form\TengstromDemoContentForm",
 *       "edit" = "Drupal\tengstrom_demo\Form\TengstromDemoContentForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "tengstrom_demo_content",
 *   data_table = "tengstrom_demo_content_field_data",
 *   revision_table = "tengstrom_demo_content_revision",
 *   revision_data_table = "tengstrom_demo_content_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer tengstrom demo content types",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "bundle" = "bundle",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log",
 *   },
 *   links = {
 *     "collection" = "/admin/content/tengstrom-demo-content",
 *     "add-form" = "/tengstrom-demo-content/add/{tengstrom_demo_content_type}",
 *     "add-page" = "/tengstrom-demo-content/add",
 *     "canonical" = "/tengstrom-demo-content/{tengstrom_demo_content}",
 *     "edit-form" = "/tengstrom-demo-content/{tengstrom_demo_content}/edit",
 *     "delete-form" = "/tengstrom-demo-content/{tengstrom_demo_content}/delete",
 *   },
 *   bundle_entity_type = "tengstrom_demo_content_type",
 *   field_ui_base_route = "entity.tengstrom_demo_content_type.edit_form",
 * )
 */
class TengstromDemoContent extends RevisionableContentEntityBase implements TengstromDemoContentInterface {
  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the demo content was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the demo content was last edited.'));

    return $fields;
  }

}
