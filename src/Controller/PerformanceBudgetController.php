<?php

namespace Drupal\performance_budget\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueWorkerManagerInterface;
use Drupal\Core\Queue\RequeueException;
use Drupal\performance_budget\Entity\PerformanceBudget;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PerformanceBudgetController.
 *
 * @package Drupal\performance_budget\Controller
 */
class PerformanceBudgetController extends ControllerBase {

  /**
   * Drupal\Core\Queue\QueueFactory definition.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queue;

  /**
   * Drupal\Core\Queue\QueueWorkerManagerInterface definition.
   *
   * @var \Drupal\Core\Queue\QueueWorkerManagerInterface
   */
  protected $queueManager;

  /**
   * Constructs a new WebPageArchiveController object.
   */
  public function __construct(QueueFactory $queue, QueueWorkerManagerInterface $queue_manager) {
    $this->queue = $queue;
    $this->queueManager = $queue_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('queue'),
      $container->get('plugin.manager.queue_worker')
    );
  }

  /**
   * Returns render array for displaying run history.
   */
  public function viewRuns($web_page_archive) {
    return [
      '#theme' => 'performance_budget',
      '#test_var' => $this->t('Test Value'),
    ];
  }

  /**
   * Returns title of the performane budget.
   */
  public function title($performance_budget) {
    return $performance_budget->label();
  }

  /**
   * Common batch processing callback for all operations.
   */
  public static function batchProcess(PerformanceBudget $performance_budget, &$context) {
    $queue = $performance_budget->getQueue();
    $queue_worker = \Drupal::service('plugin.manager.queue_worker')->createInstance('performance_budget_assessment');

    if ($item = $queue->claimItem()) {
      try {
        $queue_worker->processItem($item->data);
        $queue->deleteItem($item);
      }
      catch (RequeueException $e) {
        $queue->releaseItem($item);
      }
      catch (SuspendQueueException $e) {
        $queue->releaseItem($item);
        watchdog_exception($e);
      }
      catch (\Exception $e) {
        // In case of any other kind of exception, log it and leave the item
        // in the queue to be processed again later.
        watchdog_exception('cron', $e);
      }
    }

  }

  /**
   * Batch finished callback.
   */
  public static function batchFinished($success, $results, $operations) {
    if ($success) {
      drupal_set_message(t("The assessment has been completed."));
    }
    else {
      $error_operation = reset($operations);
      $values = [
        '@operation' => $error_operation[0],
        '@args' => print_r($error_operation[0], TRUE),
      ];
      drupal_set_message(t('An error occurred while processing @operation with arguments : @args', $values));
    }
  }

}
