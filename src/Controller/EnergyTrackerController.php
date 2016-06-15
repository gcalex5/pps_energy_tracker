<?php
//TODO: Update documentation
/**
 * Primary Controller for the module. Handles creating all of the render arrays and calling to the appropriate functions based on
 * what the user requests.
 *
 * Created by PhpStorm.
 * User: alexm
 * Date: 5/25/2016
 * Time: 1:15 PM
 * Comment for init dev branch
 */
namespace Drupal\pps_energy_tracker\Controller;

use Drupal\Core\Controller\ControllerBase;

class EnergyTrackerController extends ControllerBase {

  public function content(){
    return array(
      '#theme' => 'pps_energy_tracker',
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
    $block_id = ('generic_graph_form_block');
    $custom_block = \Drupal::service('plugin.manager.block')->createInstance($block_id, []);
    $block_content = $custom_block->build();
    return array(
      '#theme' => 'pps_energy_tracker_generic_graphs',
      '#element_content' => $block_content,
      '#graph_data' => $_SESSION['energy_tracker']['generic_graph_data'],
    );
  }
  public function electricity_graphs(){
    $block_id = ('electricity_graph_form_block');
    $custom_block = \Drupal::service('plugin.manager.block')->createInstance($block_id, []);
    $block_content = $custom_block->build();
    return array(
      '#theme' => 'pps_energy_tracker_electricity_graphs',
      '#element_content' => $block_content,
      '#graph_data' => $_SESSION['energy_tracker']['electricity_chart_data'],
    );
  }
  public function natural_gas_graphs(){
    return array(
      '#theme' => 'pps_energy_tracker_natural_gas_graphs',
    );
  }
  public function account_management(){
    $block_id = ('account_management_form_block');
    $custom_block = \Drupal::service('plugin.manager.block')->createInstance($block_id, []);
    $block_content = $custom_block->build();
    return array(
      '#theme' => 'pps_energy_tracker_account_management',
      '#element_content' => $block_content,
    );
  }
}