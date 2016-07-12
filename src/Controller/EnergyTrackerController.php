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
   *
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

  /**
   *
   * @return array
   */
  public function electricity_graphs(){
    $table_array = NULL;
    $block_id = ('electricity_graph_form_block');
    $custom_block = \Drupal::service('plugin.manager.block')->createInstance($block_id, []);
    $block_content = $custom_block->build();
    if($_SESSION['energy_tracker']['electricity_chart_account_id']){
      $table_array = $this->generateElectricityChartTable($_SESSION['energy_tracker']
        ['electricity_chart_account_id']);
    }
    return array(
      '#theme' => 'pps_energy_tracker_electricity_graphs',
      '#element_content' => $block_content,
      '#table_content' => $table_array,
      '#graph_data' => $_SESSION['energy_tracker']['electricity_chart_data'],
    );
  }

  /**
   *
   * @return array
   */
  public function natural_gas_graphs(){
    return array(
      '#theme' => 'pps_energy_tracker_natural_gas_graphs',
    );
  }

  /**
   *
   * @return array
   */
  public function account_management(){
    $block_id = ('account_management_form_block');
    $custom_block = \Drupal::service('plugin.manager.block')->createInstance($block_id, []);
    $block_content = $custom_block->build();

    $table_array = $this->generateAccountTable();

    return array(
      '#theme' => 'pps_energy_tracker_account_management',
      '#element_content' => $block_content,
      '#table_content' => $table_array,
    );
  }

  /**
   * Generate the render array containing a table of account data for the charted account
   * 
   * @param $account_id -> ID of the account we are working with
   * @return array -> Render array containing the data to construct the table.
   */
  //TODO: This does not belong here move it to another file.
  public function generateElectricityChartTable($account_id){
    //Query the data
    $query = "SELECT id,contract_start,contract_end,pricing_start FROM ppsweb_pricemodel.account WHERE id='" .
      $account_id . "' ORDER BY id";
    $queried_data = db_query($query)->fetchAllAssoc('id');
    $header =  array('ID', 'Contract Start', 'Contract End', 'Pricing Start');
    $data = array();

    //TODO: Move each series to its own separate row, add the last date/price, and a series # column
    foreach($queried_data as $row){
      $data[] = array($row->id, $row->contract_start, $row->contract_end, $row->pricing_start);
    }

    $table = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $data,
    );

    return $table;
  }

  /**
   * Generate the render array containing a table of account data for the Account Management page
   *
   * @return array -> Render array containing the data to construct the table.
   */
  //TODO: This does not belong here move it to another file
  public function generateAccountTable(){
    //1: Get the user ID, 2: Query up Business name, Utility, Target Price, Pricing Start, Contract Start, Contract End
    //3: Move it into a table and send it back to the controller to render
    $query = "SELECT id, contract_start,contract_end,pricing_start,business_name,utility_id,target_price FROM ppsweb_pricemodel.account WHERE user_id='" .
    \Drupal::currentUser()->id(). "' ORDER BY id";
    $queried_data = db_query($query)->fetchAllAssoc('id');
    $header = array('EDIT', 'ID', 'Business Name', 'Utility', 'Pricing Start', 'Contract Start', 'Contract End', 'Target Price');
    $data = array();

    foreach ($queried_data as $row){
      $data[] = array($this->t('<a href="account_management/?id='. $row->id .'">EDIT</a>'), $row->id, $row->business_name, $row->utility_id, $row->pricing_Start, $row->contract_start, $row->contract_end, $row->target_price);
    }

    $table = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $data,
    );

    return $table;
  }
}