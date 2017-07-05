<?php

namespace Drupal\performance_budget\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Performance Budget entity.
 *
 * @ConfigEntityType(
 *   id = "performance_budget",
 *   label = @Translation("Performance Budget"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\performance_budget\PerformanceBudgetListBuilder",
 *     "form" = {
 *       "add" = "Drupal\performance_budget\Form\PerformanceBudgetForm",
 *       "edit" = "Drupal\performance_budget\Form\PerformanceBudgetForm",
 *       "delete" = "Drupal\performance_budget\Form\PerformanceBudgetDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\performance_budget\PerformanceBudgetHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "performance_budget",
 *   admin_permission = "administer performance budget",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/reports/performance-budget/{performance_budget}",
 *     "add-form" = "/admin/reports/performance-budget/add",
 *     "edit-form" = "/admin/reports/performance-budget/{performance_budget}/edit",
 *     "delete-form" = "/admin/reports/performance-budget/{performance_budget}/delete",
 *     "collection" = "/admin/reports/performance-budget"
 *   }
 * )
 */
class PerformanceBudget extends ConfigEntityBase implements PerformanceBudgetInterface {

  /**
   * The Performance Budget ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Performance Budget label.
   *
   * @var string
   */
  protected $label;

}
