<?php

namespace Drupal\program\EventSubscriber;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\Event;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

class ProgramEventSubscriber implements EventSubscriberInterface {

  protected $current_user;

  protected $current_route_match;

  protected $database;


  public function __construct(AccountProxyInterface $current_user,
                              CurrentRouteMatch $current_route_match,
                              Connection $database,
                              TimeInterface $time) {

    $this->current_user = $current_user;
    $this->current_route_match = $current_route_match;
    $this->database = $database;


  }

  public static function getSubscribedEvents() {

    $events[KernelEvents::REQUEST][] = ['onRequest'];
    return $events;


  }

  public function onRequest(Event $event) {

    if ($this->current_route_match->getRouteName() == 'entity.program.canonical') {

      drupal_set_message(t('On Request @name', [
        '@name' => $this->current_user->getAccountName(),
      ]));

      $uid = $this->current_user->id();
      $pr_id = $this->current_route_match->getParameter('program')->id();

      $storage_budget = \Drupal::entityTypeManager()->getStorage('program')->load($pr_id);

      $budget = $storage_budget->field_budget->getString();

      $this->database->insert('program_history')
                      ->fields([
                        'pid' => $pr_id,
                        'uid' => $uid,
                        'budget' => $budget,
                      ])->execute();

    }

    if ($this->current_route_match->getRouteName() == 'entity.program.delete_form') {

      $pr_id = $this->current_route_match->getParameter('program')->id();

      $this->database->delete('program_history')
                      ->condition('pid', $pr_id)
                      ->execute();

    }

  }

}