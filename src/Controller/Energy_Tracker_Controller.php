<?php
/**
 * Created by PhpStorm.
 * User: alexm
 * Date: 5/25/2016
 * Time: 1:15 PM
 * Comment for init dev branch
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

    /**
     * Generic Charts Controller function
     * Called on page load.
     * Form function then takes over.
     * AJAX -> Calls to the Generic_Charts_Controller file to generate the graphing points and draws the graph
     * @return array
     */
    public function generic_graphs(){
        return array(
            '#theme' => 'pps_energy_tracker_generic_graphs',
            '#test_var' => $this->t('Test Var 1'),
        );
    }
    public function electricity_graphs(){
        return array(
            '#theme' => 'pps_energy_tracker_electricity_graphs',
            '#test_var' => $this->t('Test Var 1'),
        );
    }
    public function natural_gas_graphs(){
        return array(
            '#theme' => 'pps_energy_tracker_natural_gas_graphs',
            '#test_var' => $this->t('Test Var 1'),
        );
    }
    public function account_management(){
        return array(
            '#theme' => 'pps_energy_tracker_account_management',
            '#test_var' => $this->t('Test Var 1'),
        );
    }
}