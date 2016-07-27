<?php
//TODO: Documentation
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 6/7/2016
 * Time: 11:06 AM
 */

namespace Drupal\pps_energy_tracker\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Account Management Form Block for PPS ET
 * @Block(
 *   id = "account_management_form_block",
 *   admin_label = @Translation("Account Management Form Block"),
 * )
 */
class AccountManagementFormBlock extends BlockBase{

  /**
   * { @inheritdoc }
   * @return array - AccountManagementForm
   */
  public function build(){
    if($_GET['id'] == null){
      return \Drupal::formBuilder()
        ->getForm('Drupal\pps_energy_tracker\Form\AccountManagementForm');
    }
    return \Drupal::formBuilder()
      ->getForm('Drupal\pps_energy_tracker\Form\AccountManagementForm');
  }
}