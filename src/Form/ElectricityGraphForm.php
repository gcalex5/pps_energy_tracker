<?php
//TODO: Documentation
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/15/16
 * Time: 4:33 PM
 */

namespace Drupal\pps_energy_tracker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\pps_energy_tracker\Controller\ElectricityChartsController;

class ElectricityGraphForm extends FormBase{

  /**
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'electricity_graph_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
    $accountDataSet = $this->queryAccountData();
    $form['graph_name'] = array(
      '#type' => 'select',
      '#title' => 'Graph: ',
      '#options' => $accountDataSet,
      '#description' => $this->t('Select A Graph'),
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit')
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array  &$form, FormStateInterface $form_state){
    //TODO: Validate form input
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state){
    $account_id = $form['graph_name']['#value'];
    $electricity_controller = new ElectricityChartsController();
    $_SESSION['energy_tracker']['electricity_chart_data'] = $electricity_controller
      ->pricingController($account_id);
    $_SESSION['energy_tracker']['electricity_chart_account_id'] = $account_id;
    $_SESSION['energy_tracker']['electricity_chart_name'] = $form['graph_name']['#options'][$account_id];
  }

  public function queryAccountData(){
    $transformedAccountData = array();
    //Query the data
    $query = "SELECT id,usage_id,business_name FROM ppsweb_pricemodel.account "
      . "WHERE user_id='" . \Drupal::currentUser()->id() . "' ORDER BY id";

    //TODO: rewrite query
    $accountDataSet = db_query($query)->fetchAllAssoc('usage_id');
    
    foreach($accountDataSet as &$value){
      $transformedAccountData[$value->id] = $value->business_name;
    }
    return $transformedAccountData;
  }
}