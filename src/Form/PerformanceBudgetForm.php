<?php

namespace Drupal\performance_budget\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the Performance Budget add and edit forms.
 */
class PerformanceBudgetForm extends EntityForm {

  /**
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQueryFactory;

  /**
   * Constructs a PerformanceBudgetForm.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The entity query.
   */
  public function __construct(QueryFactory $query_factory) {
    $this->entityQueryFactory = $query_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.query'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $performance_budget = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $performance_budget->label,
      '#description' => $this->t('Label for the performance budget.'),
      '#required' => TRUE,
    ];
    $form['schedule'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Schedule'),
      '#maxlength' => 255,
      '#default_value' => $performance_budget->schedule,
      '#description' => $this->t('Schedule for the performance budget. Uses CRON expressions - https://en.wikipedia.org/wiki/Cron#CRON_expression'),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $performance_budget->id,
      '#machine_name' => [
        'exists' => [$this, 'exists'],
      ],
      '#disabled' => !$performance_budget->isNew(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $performance_budget = $this->entity;
    $status = $performance_budget->save();

    if ($status) {
      drupal_set_message($this->t('The %label performance budget was created.', [
        '%label' => $performance_budget->label(),
      ]));
    }
    else {
      drupal_set_message($this->t('The %label performance budget was not saved.', [
        '%label' => $performance_budget->label(),
      ]));
    }

    $form_state->setRedirect('entity.performance_budget.collection');
  }
  /**
   * Checks for an existing robot.
   *
   * @param string|int $entity_id
   *   The entity ID.
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return bool
   *   TRUE if this format already exists, FALSE otherwise.
   */
  public function exists($entity_id, array $element, FormStateInterface $form_state) {
    // Use the query factory to build a new robot entity query.
    $query = $this->entityQueryFactory->get('performance_budget');

    // Query the entity ID to see if its in use.
    $result = $query->condition('id', $element['#field_prefix'] . $entity_id)
      ->execute();

    // We don't need to return the ID, only if it exists or not.
    return (bool) $result;
  }

}
