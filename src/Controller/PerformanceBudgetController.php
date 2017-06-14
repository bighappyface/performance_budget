<?php

namespace Drupal\performance_budget\Controller;

use Drupal\performance_budget\PerformanceBudgetInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller routines for performance budget routes.
 */
class PerformanceBudgetController extends ControllerBase {

  /**
   * Calls a method on a performance budget and reloads the listing page.
   *
   * @param \Drupal\performance_budget\PerformanceBudgetInterface $block
   *   The performance budget being acted upon.
   * @param string $op
   *   The operation to perform, e.g., 'enable' or 'disable'.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect back to the listing page.
   */
  public function performOperation(PerformanceBudgetInterface $budget, $op) {
    $budget->$op()->save();
    drupal_set_message($this->t('The performance budget settings have been updated.'));
    return $this->redirect('entity.performance_budget.collection');
  }

}
