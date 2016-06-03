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
use Drupal\block_content\Entity\BlockContent;

class Energy_Tracker_Controller extends ControllerBase {

    public function content(){
        return array(
            '#theme' => 'pps_energy_tracker',
        );
    }

    //TODO: Update documentation
    /**
     * Generic Charts Controller function
     * Called on page load.
     * Form function then takes over.
     * AJAX -> Calls to the Generic_Charts_Controller file to generate the graphing points and draws the graph
     * @return array
     */
    public function generic_graphs(){
        $block_id = ('generic_graph_form_block');
        $custom_block = \Drupal::service('plugin.manager.block')->createInstance($block_id, []);
        $block_content = $custom_block->build();
        $foo = 'pause';
        return array(
          '#theme' => 'pps_energy_tracker_generic_graphs',
          '#element_content' => $block_content,
          '#graph_data' => $_SESSION['energy_tracker']['generic_graph_data'][0],
        );
    }
    public function electricity_graphs(){
        return array(
            '#theme' => 'pps_energy_tracker_electricity_graphs',
        );
    }
    public function natural_gas_graphs(){
        return array(
            '#theme' => 'pps_energy_tracker_natural_gas_graphs',
        );
    }
    public function account_management(){
        return array(
            '#theme' => 'pps_energy_tracker_account_management',
        );
    }
}