<?php

  namespace Drupal\budget\Plugin\Block;

  use Drupal\Core\Access\AccessResult;
  use Drupal\Core\Block\BlockBase;
  use Drupal\Core\Session\AccountInterface;

  /**
   * Provides a budget block.
   *
   * @Block(
   *   id = "budget_block",
   *   admin_label = @Translation("Budget")
   * )
   */
  class Budget extends BlockBase {

    /**
     * Implements Drupal\Core\Block\BlockBase::build().
     */
    public function build() {

      $current_pid = \Drupal::routeMatch()->getParameter('program');

      $pid = $current_pid->id();

      $storage = \Drupal::entityTypeManager()->getStorage('webform_submission');

      $webform_by_pid = $storage->loadByProperties([
        'entity_type' => 'program',
        'entity_id' => $pid,
      ]);

      $submission_data = array();

      foreach ($webform_by_pid as $submission) {

        $submission_data[] = $submission->getData();

      }

      foreach($submission_data as $val) {

        $hotel = $val;

      }

      $hotel_id = $hotel['hotel'];

      $date_depart = date_create($hotel['date_de_depart']);

      $date_arrivee = date_create($hotel['date_d_arrivee']);

      $hotel_time = date_diff($date_depart,$date_arrivee,absolute);

      $days_number = $hotel_time->days;

      $storage_hotels =  \Drupal::entityTypeManager()->getStorage('node')->load($hotel_id);

      $hotel_price = $storage_hotels->field_room_price->getString();

      $total_hotel = $days_number * $hotel_price;

      $budget = \Drupal::database()->select('program_history', 'pgrh')
                                            ->fields('pgrh',['budget'])
                                            ->condition('pid', $pid)
                                            ->execute()
                                            ->fetchField();
      //ksm($budget);
      if($budget - $total_hotel < 0) {

        $build = [
          '#markup' => $this->t('Budget restant : %budget  Euros',
            ['%budget' => $budget]),
          '#cache' => [
            'keys' => ['budget:block'],
            'max-age' => '0',
          ],
        ];

      }
      else {

        $budget_calcul = $budget - $total_hotel;

        $budget = \Drupal::database()->update('program_history')
                                      ->fields([
                                            'budget' => $budget_calcul,
                                              ])
                                      ->execute();

        $build = [
          '#markup' => $this->t('Budget restant : %budget  Euros',
            ['%budget' => $budget_calcul]),
          '#cache' => [
            'keys' => ['budget:block'],
            'max-age' => '0',
          ],
        ];
      }

      return $build;

    }

    /**
     * @param \Drupal\Core\Session\AccountInterface $account
     *
     * @return \Drupal\Core\Access\AccessResult
     */
    public function blockAccess(AccountInterface $account) {

      return AccessResult::allowedIfHasPermission($account, 'permission budget');

    }

  }