<?php
/**
 * Handles creating the form block located on /energy_tracker/electricity_graph
 */
namespace Drupal\pps_energy_tracker\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Electricity Graph Form Block for PPS ET
 * @Block(
 *   id = "electricity_graph_form_block",
 *   admin_label = @Translation("Electricity Graph Form Block"),
 * )
 */

class ElectricityGraphFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array - ElectricityGraphForm
   */
  public function build(){
    return \Drupal::formBuilder()
      ->getForm('Drupal\pps_energy_tracker\Form\ElectricityGraphForm');
  }
}