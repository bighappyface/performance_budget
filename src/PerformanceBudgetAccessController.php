<?php

namespace Drupal\performance_budget;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access controller for the robot entity.
 *
 * We set this class to be the access controller in Robot's entity annotation.
 *
 * @see \Drupal\performance_budget\Entity\PerformanceBudget
 */
class PerformanceBudgetAccessController extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if (in_array($operation, ['enable', 'disable'])) {
      return TRUE;
    }
    return parent::checkAccess($entity, $operation, $account);
  }

}
