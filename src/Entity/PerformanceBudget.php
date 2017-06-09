<?php

namespace Drupal\performance_budget\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\performance_budget\PerformanceBudgetInterface;

/**
 * Defines the Performance Budget entity.
 *
 * @ConfigEntityType(
 *   id = "performance_budget",
 *   label = @Translation("Performance Budget"),
 *   handlers = {
 *     "list_builder" = "Drupal\performance_budget\Controller\PerformanceBudgetListBuilder",
 *     "form" = {
 *       "add" = "Drupal\performance_budget\Form\PerformanceBudgetForm",
 *       "edit" = "Drupal\performance_budget\Form\PerformanceBudgetForm",
 *       "delete" = "Drupal\performance_budget\Form\PerformanceBudgetDeleteForm",
 *     }
 *   },
 *   config_prefix = "performance_budget",
 *   admin_permission = "administer performance budget",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/development/performance-budget/{performance_budget}",
 *     "delete-form" = "/admin/config/development/performance-budget/{performance_budget}/delete",
 *   }
 * )
 */
class PerformanceBudget extends ConfigEntityBase implements PerformanceBudgetInterface {

  /**
   * The Performance Budget ID.
   *
   * @var string
   */
  public $id;

  /**
   * The Performance Budget label.
   *
   * @var string
   */
  public $label;

  // Your specific configuration property get/set methods go here,
  // implementing the interface.
}
