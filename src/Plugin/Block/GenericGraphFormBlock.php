<?php

namespace Drupal\pps_energy_tracker\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Generic Graph Form Block to PPS ET
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
        //return a form for the custom block
        return \Drupal::formBuilder()->getForm('Drupal\pps_energy_tracker\Form\GenericGraphForm');
    }
}