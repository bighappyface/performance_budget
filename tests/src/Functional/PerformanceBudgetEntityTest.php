<?php

namespace Drupal\Tests\performance_budget\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests performance budget .
 *
 * @group performance_budget
 */
class PerformanceBudgetEntityTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public $profile = 'minimal';

  /**
   * User.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'performance_budget',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser([
      'administer performance budget',
    ]);
  }

  /**
   * Functional test of performance budget entity.
   */
  public function testPerformanceBudgetEntity() {
    $assert = $this->assertSession();
    // Login.
    $this->drupalLogin($this->user);
    // Verify list exists with add button.
    $this->drupalGet('admin/config/development/performance-budget');
    $this->assertLinkByHref('admin/config/development/performance-budget/add');
    // Add an entity using the entity form.
    $this->drupalGet('admin/config/development/performance-budget/add');
    $this->drupalPostForm(
      NULL,
      [
        'label' => 'Test Budget',
        'id' => 'test_budget',
        'schedule' => '0 * * * *',
      ],
      t('Save')
    );
    $assert->pageTextContains('The Test Budget performance budget was created.');
    // Verify form will not accept invalid CRON expression.
    // $this->drupalGet('admin/config/development/performance-budget/add');
    // $this->drupalPostForm(
    //   NULL,
    //   [
    //     'label' => 'Test Budget',
    //     'schedule' => 'foobar',
    //   ],
    //   t('Save')
    // );
    // $assert->pageTextContains('Schedule field is required.');
  }

}
