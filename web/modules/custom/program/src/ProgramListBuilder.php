<?php

namespace Drupal\program;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Program entities.
 *
 * @ingroup program
 */
class ProgramListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    //$header['id'] = $this->t('Program ID');
    $header['Name'] = $this->t('name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\program\Entity\Program */
    //$row['id'] = $entity->id();
    $row['Name'] = Link::createFromRoute(
      $entity->label(),
      'entity.program.canonical',
      ['program' => $entity->id()]
    );

    return $row + parent::buildRow($entity);
  }

}
