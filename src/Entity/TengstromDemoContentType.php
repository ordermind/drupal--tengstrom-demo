<?php

declare(strict_types=1);

namespace Drupal\tengstrom_demo\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Demo Content type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "tengstrom_demo_content_type",
 *   label = @Translation("Demo Content type"),
 *   label_collection = @Translation("Demo Content types"),
 *   label_singular = @Translation("demo content type"),
 *   label_plural = @Translation("demo content types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count demo content type",
 *     plural = "@count demo content types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\tengstrom_demo\Form\TengstromDemoContentTypeForm",
 *       "edit" = "Drupal\tengstrom_demo\Form\TengstromDemoContentTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\tengstrom_demo\TengstromDemoContentTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   admin_permission = "administer demo content types",
 *   bundle_of = "tengstrom_demo_content",
 *   config_prefix = "tengstrom_demo_content_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/tengstrom_demo_content_types/add",
 *     "edit-form" = "/admin/structure/tengstrom_demo_content_types/manage/{tengstrom_demo_content_type}",
 *     "delete-form" = "/admin/structure/tengstrom_demo_content_types/manage/{tengstrom_demo_content_type}/delete",
 *     "collection" = "/admin/structure/tengstrom_demo_content_types"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *   }
 * )
 */
class TengstromDemoContentType extends ConfigEntityBundleBase {

  /**
   * The machine name of this demo content type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the demo content type.
   *
   * @var string
   */
  protected $label;

}
