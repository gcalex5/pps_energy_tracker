<?php
/**
 * Handles creating the form block located on /energy_tracker/generic_graph
 */
namespace Drupal\pps_energy_tracker\Plugin\Block;

//TODO: Test without AccessResult, FormStateInterface, and AccountInterface includes
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

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
   */
  public function build(){
      return \Drupal::formBuilder()->getForm('Drupal\pps_energy_tracker\Form\GenericGraphForm');
  }
}