<?php

namespace Drupal\program\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Program entities.
 */
class ProgramViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    $data['program_history']['table']['group']= t('program history');
    $data['program_history']['table']['provider']= 'program';
    $data['program_history']['table']['base'] = [
      'field' => 'id',
      'title'=>t('program history ID'),
      'weight'=> -100,
    ];
    $data['program_history']['uid']= [
      'title'=>t('user id'),
      'help'=> t('Author program Id'),
      'field'=>['id'=>'numeric'],
      'relationship'=>[
        'base'=>'users_field_data',
        'base field'=>'uid',
        'id'=>'standard',
        'label' => t('program user id'),
      ],
    ];
    $data['program_history']['pid']= [
      'title'=>t('program Id'),
      'field'=>['id'=>'numeric'],
      'relationship'=>[
        'base'=>'program_field_data',
        'base field' => 'id',
        'id'=>'standard',
        'label'=> t('Program id'),
      ],
      'primary key' => ['id'],
    ];


    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }
  
}
