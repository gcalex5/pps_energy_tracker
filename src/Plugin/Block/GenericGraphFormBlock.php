<?php
/**
 * Handles creating the form block located on /energy_tracker/generic_graph
 */
namespace Drupal\pps_energy_tracker\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Generic Graph Form Block for PPS ET
 * @Block(
 *   id = "generic_graph_form_block",
 *   admin_label = @Translation("Generic Graph Form Block"),
 * )
 */

class GenericGraphFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array - GenericGraphForm
   */
  public function build(){
      return \Drupal::formBuilder()->getForm('Drupal\pps_energy_tracker\Form\GenericGraphForm');
  }
}