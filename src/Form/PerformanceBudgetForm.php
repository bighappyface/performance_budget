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
   * Constructs a PerformanceBudgetForm
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The entity query.
   */
  public function __construct(EntityManagerInterface $entity_manager, QueryFactory $entity_query) {
    $this->entityManager = $entity_manager;
    $this->entityQuery = $entity_query;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $performance_budget = $this->entity;

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $performance_budget->label(),
      '#description' => $this->t("Label for the performance budget."),
      '#required' => TRUE,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $performance_budget->id(),
      '#machine_name' => array(
        'exists' => array($this, 'exist'),
      ),
      '#disabled' => !$performance_budget->isNew(),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $performance_budget = $this->entity;
    $status = $performance_budget->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label performance budget.', array(
        '%label' => $performance_budget->label(),
      )));
    }
    else {
      drupal_set_message($this->t('The %label performance budget was not saved.', array(
        '%label' => $performance_budget->label(),
      )));
    }

    $form_state->setRedirect('entity.performance_budget.collection');
  }

  /**
   * Helper function to check whether an PerformanceBudget configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityQuery->get('performance_budget')
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
