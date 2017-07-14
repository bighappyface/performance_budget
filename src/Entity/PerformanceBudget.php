<?php

namespace Drupal\performance_budget\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\Core\Plugin\DefaultLazyPluginCollection;
use Drupal\performance_budget\Plugin\AssessmentUtilityInterface;
use GuzzleHttp\HandlerStack;

/**
 * Defines the Performance Budget entity.
 *
 * @ConfigEntityType(
 *   id = "performance_budget",
 *   label = @Translation("Performance Budget"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\performance_budget\Entity\PerformanceBudgetListBuilder",
 *     "form" = {
 *       "add" = "Drupal\performance_budget\Form\PerformanceBudgetForm",
 *       "edit" = "Drupal\performance_budget\Form\PerformanceBudgetForm",
 *       "delete" = "Drupal\performance_budget\Form\PerformanceBudgetDeleteForm",
 *       "queue" = "Drupal\performance_budget\Form\PerformanceBudgetQueueForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\performance_budget\Entity\Routing\PerformanceBudgetHtmlRouteProvider",
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
 *     "queue-form" = "/admin/reports/performance-budget/{performance_budget}/queue",
 *     "collection" = "/admin/reports/performance-budget"
 *   },
 *   config_export = {
 *     "id",
 *     "uuid",
 *     "label",
 *     "cron_schedule",
 *     "assessment_utility",
 *     "assessment_utilities",
 *     "assessments"
 *   }
 * )
 */
class PerformanceBudget extends ConfigEntityBase implements PerformanceBudgetInterface, EntityWithPluginCollectionInterface {

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

  /**
   * The cron schedule.
   *
   * @var string
   */
  protected $cron_schedule;

  /**
   * The assigned assessment utility.
   *
   * @var string
   */
  protected $assessment_utility;

  /**
   * The array of assessment utilities for this performance budget.
   *
   * @var array
   */
  protected $assessment_utilities = [];

  /**
   * Holds the collection of assessment utilities that are in use.
   *
   * @var \Drupal\Core\Plugin\DefaultLazyPluginCollection
   */
  protected $assessent_utility_collection;

  /**
   * Holds run data.
   *
   * @var array
   */
  protected $assessments = [];

  /**
   * Retrieves the Cron schedule.
   */
  public function getCronSchedule() {
    return $this->cron_schedule;
  }

  /**
   * {@inheritdoc}
   */
  public function getAssessmentUtility($assessment_utility) {
    return $this->getAssessmentUtilities()->get($assessment_utility);
  }

  /**
   * {@inheritdoc}
   */
  public function getAssessmentUtilities() {
    if (!$this->assessent_utility_collection) {
      $this->assessent_utility_collection = new DefaultLazyPluginCollection($this->assessmentUtilityPluginManager(), $this->assessment_utilities);
    }
    return $this->assessent_utility_collection;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    return ['assessment_utilities' => $this->getAssessmentUtilities()];
  }

  /**
   * {@inheritdoc}
   */
  public function addAssessmentUtility(array $configuration) {
    $configuration['uuid'] = $this->uuidGenerator()->generate();
    $this->getAssessmentUtilities()->addInstanceId($configuration['uuid'], $configuration);
    return $configuration['uuid'];
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAssessmentUtility(AssessmentUtilityInterface $assessment_utility) {
    $this->getAssessmentUtilities()->removeInstanceId($assessment_utility->getUuid());
    return $this;
  }

  /**
   * Determines if entity has an instance of the specified plugin id.
   *
   * @param string $id
   *   Capture utility plugin id.
   */
  public function hasAssessmentUtilityInstance($id) {
    foreach ($this->assessment_utilities as $utility) {
      if ($utility['id'] == $id) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Deletes an assessment utility by id.
   *
   * @param string $id
   *   Assessment utility plugin id.
   */
  public function deleteAssessmentUtilityById($id) {
    foreach ($this->assessment_utilities as $utility) {
      if ($utility['id'] == $id) {
        $this->getAssessmentUtilities()->removeInstanceId($utility['uuid']);
      }
    }
    return $this;
  }

  /**
   * Retrieves count of number of jobs in queue.
   *
   * @var int
   */
  public function getQueueCt() {
    $queue = $this->getQueue();
    return (isset($queue)) ? $queue->numberOfItems() : 0;
  }

  /**
   * Retrieves count of number of completed runs.
   *
   * @var int
   */
  public function getRunCt() {
    // TODO: Implement this.
    return 0;
  }

  /**
   * Retrieves the queue for the performance budget.
   *
   * @return \Drupal\Core\Queue\QueueInterface
   *   Queue object for this particular performance budget.
   */
  public function getQueue() {
    return \Drupal::service('queue')->get("performance_budget_assessment.{$this->uuid()}");
  }

  /**
   * Queues the assessment to run.
   */
  public function startNewRun(HandlerStack $handler = NULL) {
    try {

      $queue = $this->getQueue();
      $run_uuid = $this->uuidGenerator()->generate();

      foreach ($urls as $url) {
        foreach ($this->getAssessmentUtilities() as $utility) {
          $item = [
            'performance_budget' => $this,
            'utility' => $utility,
            'url' => $url,
            'run_uuid' => $run_uuid,
          ];
          $queue->createItem($item);
        }
      }

      $this->storeNewRun($run_uuid, $queue->numberOfItems());
    }
    catch (\Exception $e) {
      drupal_set_message($e->getMessage(), 'warning');
    }
  }

  /**
   * Stores run info into the database.
   */
  protected function storeNewRun($uuid, $queue_ct) {
    $config = $this->getEditableConfig();
    $new_run = [
      'uuid' => $uuid,
      'timestamp' => \Drupal::service('datetime.time')->getCurrentTime(),
      'queue_ct' => $queue_ct,
      'status' => 'pending',
      'assessments' => [],
    ];

    $config->set("assessments.{$uuid}", $new_run);
    $config->save();
  }

  /**
   * Marks an assessment task complete.
   */
  public function markCaptureComplete($data) {
    // TODO: Move functionality into controller?
    $config = $this->getEditableConfig();
    $uuid = $this->uuidGenerator()->generate();
    $capture = [
      'uuid' => $uuid,
      'timestamp' => \Drupal::service('datetime.time')->getCurrentTime(),
      'status' => 'complete',
      'capture_type' => $data['capture_response']->getType(),
      'content' => $data['capture_response']->getContent(),
    ];
    $config->set("runs.{$data['run_uuid']}.captures.{$uuid}", $capture);
    $config->save();
  }

  /**
   * Wraps the assessment utility plugin manager.
   *
   * @return \Drupal\Component\Plugin\PluginManagerInterface
   *   An assessment plugin manager object.
   */
  protected function assessmentUtilityPluginManager() {
    return \Drupal::service('plugin.manager.assessment_utility');
  }

  /**
   * Retrieves an editable config for this entity.
   *
   * @return \Drupal\Core\Config\Config
   *   A config object for the current entity.
   */
  protected function getEditableConfig() {
    return \Drupal::service('config.factory')->getEditable("performance_budget.performance_budget.{$this->id()}");
  }

}
