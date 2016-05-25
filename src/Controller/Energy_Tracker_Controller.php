<?php
/**
 * Created by PhpStorm.
 * User: alexm
 * Date: 5/25/2016
 * Time: 1:15 PM
 */

//TODO: refactor this directory to lowercase. Changes will need to be made in the routing and PHP file unless we are refactoring all packages to uppercase.
namespace Drupal\pps_energy_tracker\Controller;
use Drupal\Core\Controller\ControllerBase;

class Energy_Tracker_Controller extends ControllerBase {

    //TODO: Remove test variable once everything is correctly mapped
    public function content(){
        return array(
            '#theme' => 'pps_energy_tracker',
            '#test_var' => $this->t('Test Var 1'),
        );
    }
}