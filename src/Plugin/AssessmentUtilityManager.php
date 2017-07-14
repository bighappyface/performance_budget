<?php

namespace Drupal\performance_budget\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the AssessmentUtility plugin manager.
 */
class AssessmentUtilityManager extends DefaultPluginManager {

  /**
   * Constructs a new PerformanceBudgetPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/PerformanceBudgetPlugin', $namespaces, $module_handler, 'Drupal\performance_budget\Plugin\AssessmentUtilityInterface', 'Drupal\performance_budget\Annotation\AssessmentUtility');
    $this->alterInfo('performance_budget_assessment_utility_info');
    $this->setCacheBackend($cache_backend, 'performance_budget_assessment_utility_plugins');
  }

}
