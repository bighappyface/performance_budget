<?php

namespace Drupal\performance_budget\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an Assessment utility item annotation object.
 *
 * @see \Drupal\web_page_archive\Plugin\AssessmentUtilityManager
 * @see plugin_api
 *
 * @Annotation
 */
class AssessmentUtility extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
