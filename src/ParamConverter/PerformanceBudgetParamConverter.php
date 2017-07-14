<?php

namespace Drupal\performance_budget\ParamConverter;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Symfony\Component\Routing\Route;

/**
 * Converts performance_budget parameter ids to entities.
 */
class PerformanceBudgetParamConverter implements ParamConverterInterface {

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    return \Drupal::entityTypeManager()->getStorage('performance_budget')->load($value);
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    return (!empty($definition['type']) && $definition['type'] == 'performance_budget');
  }

}
