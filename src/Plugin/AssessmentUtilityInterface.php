<?php

namespace Drupal\performance_budget\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Performance Assessment Utility plugins.
 */
interface AssessmentUtilityInterface extends PluginInspectionInterface {

  /**
   * Performs a performance assessment.
   *
   * @param array $data
   *   Array containing assessment info.
   *
   * @return AssessmentUtilityInterface
   *   Returns reference to self.
   */
  public function assess(array $data);

  /**
   * Retrieves response from most recent assessment.
   *
   * @return Drupal\performance_budget\Plugin\AssessmentUtilityInterface
   *   An assessment response object.
   */
  public function getResponse();

}
