<?php
//TODO: Documentation
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 6/7/2016
 * Time: 10:20 AM
 */

namespace Drupal\pps_energy_tracker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AccountManagementForm extends FormBase{

  /**
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'AccountManagementForm';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
    //Personal Information
    $form['bus_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Business Name'),
      '#size' => 20,
      '#maxlength' => 128,
      '#prefix' => "<div class='account-fields'>",
      '#required' => TRUE,
    );

    //TODO: Swap this into a database call to the utility table
    $form['utility'] = array(
      '#type' => 'select',
      '#title' => $this->t('Utility Name'),
      '#options' => ['A', 'B', 'C'],
      '#required' => TRUE,
    );

    $form['pricing_start'] = array(
      '#type' => 'date',
      '#title' => $this->t('Pricing Start'),
      '#required' => TRUE,
    );

    $form['contract_s_1'] = array(
      '#type' => 'date',
      '#title' => $this->t('Contract Start'),
      '#required' => TRUE,
    );
    $form['contract_e_1'] = array(
      '#type' => 'date',
      '#title' => $this->t('Contract End'),
      '#required' => TRUE,
    );
    $form['contract_s_2'] = array(
      '#type' => 'date',
      '#title' => $this->t('Contract Start 2'),
      '#required' => TRUE,
    );
    $form['contract_e_2'] = array(
      '#type' => 'date',
      '#title' => $this->t('Contract End 2'),
      '#required' => TRUE,
    );
    $form['contract_s_3'] = array(
      '#type' => 'date',
      '#title' => $this->t('Contract Start 3'),
      '#required' => TRUE,
    );
    $form['contract_e_3'] = array(
      '#type' => 'date',
      '#title' => $this->t('Contract End 3'),
      '#required' => TRUE,
    );

    $form['cap_obligation'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Capacity Obligation'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['target_price'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Target Price'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['adtl_cost'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Additional Costs'),
      '#size' => 20,
      '#maxlength' => 128,
      '#suffix' => "</div>",
      '#required' => TRUE,
    );

    //Monthly Usage
    $form['jan_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Jan Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#prefix' =>"<div class='usage-fields'>",
      '#required' => TRUE,
    );
    $form['feb_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Feb Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['mar_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Mar Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['apr_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Apr Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['may_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('May Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['jun_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Jun Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['jul_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Jul Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['aug_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Aug Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['sep_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Sep Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['oct_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Oct Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['nov_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Nov Usage'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['dec_usage'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Dec Usage'),
      '#size' => 20,
      '#maxlength'=> 128,
      '#suffix' => "</div>",
      '#required' => TRUE,
    );
    
    //On and Off Peak Percentages
    $form['on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('On Peak'),
      '#size' => 20,
      '#maxlength'=>128,
      '#prefix' => "<div class='peak-fields'>",
      '#required' => TRUE,
    );
    $form['off_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Off Peak'),
      '#size' => 20,
      '#maxlength'=>128,
      '#required' => TRUE,
    );
    $form['jan_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Jan On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['feb_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Feb On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['mar_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Mar On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['apr_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Apr On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['may_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('May On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['jun_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Jun On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['jul_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Jul On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['aug_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Aug On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['sep_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Sep On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['oct_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Oct On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['nov_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Nov On Peak'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['dec_on_peak'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Dec On Peak'),
      '#size' => 20,
      '#maxlength'=> 128,
      '#suffix' => "</div>",
      '#required' => TRUE,
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
    //Pull the form data into variables
    $bus_name = $form_state->getValue('bus_name');
    $pricing_start = $form_state->getValue('pricing_start');
    $contract_s_1 = $form_state->getValue('contract_s_1');
    $contract_e_1 = $form_state->getValue('contract_e_1');
    $contract_s_2 = $form_state->getValue('contract_s_2');
    $contract_e_2 = $form_state->getValue('contract_e_2');
    $contract_s_3 = $form_state->getValue('contract_s_3');
    $contract_e_3 = $form_state->getValue('contract_e_3');
    $cap_obligation = $form_state->getValue('cap_obligation');
    $target_price = $form_state->getValue('target_price');
    $adtl_cost = $form_state->getValue('adtl_cost');
    $jan_usage = $form_state->getValue('jan_usage');
    $feb_usage = $form_state->getValue('feb_usage');
    $mar_usage = $form_state->getValue('mar_usage');
    $apr_usage = $form_state->getValue('apr_usage');
    $may_usage = $form_state->getValue('may_usage');
    $jun_usage = $form_state->getValue('jun_usage');
    $jul_usage = $form_state->getValue('jul_usage');
    $aug_usage = $form_state->getValue('aug_usage');
    $sep_usage = $form_state->getValue('sep_usage');
    $oct_usage = $form_state->getValue('oct_usage');
    $nov_usage = $form_state->getValue('nov_usage');
    $dec_usage = $form_state->getValue('dec_usage');
    $on_peak = $form_state->getValue('on_peak');
    $off_peak = $form_state->getValue('off_peak');
    $jan_on_peak = $form_state->getValue('jan_on_peak');
    $feb_on_peak = $form_state->getValue('feb_on_peak');
    $mar_on_peak = $form_state->getValue('mar_on_peak');
    $apr_on_peak = $form_state->getValue('apr_on_peak');
    $may_on_peak = $form_state->getValue('may_on_peak');
    $jun_on_peak = $form_state->getValue('jun_on_peak');
    $jul_on_peak = $form_state->getValue('jul_on_peak');
    $aug_on_peak = $form_state->getValue('aug_on_peak');
    $sep_on_peak = $form_state->getValue('sep_on_peak');
    $oct_on_peak = $form_state->getValue('oct_on_peak');
    $nov_on_peak = $form_state->getValue('nov_on_peak');
    $dec_on_peak = $form_state->getValue('dec_on_peak');

    /**
     * Store the account usage details
     */
    $query = "INSERT INTO ppsweb_pricemodel.account_usage " . 
      "(adtl_cost, cap_obligation, on_peak_percent, off_peak_percent, jan_usage," .
      " feb_usage, mar_usage, apr_usage, may_usage, jun_usage, jul_usage," .
      " aug_usage, sept_usage, oct_usage, nov_usage, dec_usage, jan_on_peak," . 
      " feb_on_peak, mar_on_peak, apr_on_peak, may_on_peak, jun_on_peak,". 
      " jul_on_peak, aug_on_peak, sept_on_peak, oct_on_peak, nov_on_peak," . 
      " dec_on_peak) VALUES ("
      . $adtl_cost . ", " . $cap_obligation . ", " . $on_peak . ", " . $off_peak 
      . ", " . $jan_usage . ", " . $feb_usage . ", " . $mar_usage . ", " 
      . $apr_usage . ", " . $may_usage . ", " . $jun_usage . ", " . $jul_usage 
      . ", " . $aug_usage . ", " . $sep_usage . ", " . $oct_usage . ", " 
      . $nov_usage . ", " . $dec_usage . ", " . $jan_on_peak .", " . $feb_on_peak
      . ", " . $mar_on_peak . ", " .$apr_on_peak . ", " . $may_on_peak . ", " 
      . $jun_on_peak . ", " . $jul_on_peak . ", " . $aug_on_peak . ", " 
      . $sep_on_peak . ", " . $oct_on_peak . ", " . $nov_on_peak . ", " 
      . $dec_on_peak . ")";
    
    //TODO: rewrite query
    db_query($query);

    /**
     * Get the usage_id for what we just inserted
     */
    $second_query = "SELECT last_insert_id() AS id";
    
    //TODO: rewrite query
    $usage_id = db_query($second_query)->fetchAll()[0]->id;

    /**
     * Store the account details
     */
    //TODO: Utility ID should be dynamically set with the $utility variable. Current set to 3 as a default making it 'OH - Ohio Edison'
    $third_query = "INSERT INTO ppsweb_pricemodel.account (user_id, utility_id, " 
      . "usage_id, business_name, pricing_start, " .
      "contract_start, contract_end, contract_start_2, contract_end_2, contract_start_3, " .
      "contract_end_3, target_price, last_price, last_date) VALUES ("
      . \Drupal::currentUser()->id() . ", 3," . $usage_id . ", '" . $bus_name 
      . "', '" . $pricing_start . "', '" . $contract_s_1 . "', '" 
      . $contract_e_1 . "', '". $contract_s_2 . "', '" . $contract_e_2 . "', '" 
      . $contract_s_3 . "', '" . $contract_e_3 . "', " . $target_price . ", 0.025, " . "'2016-06-25')";
    
    //TODO: rewrite query
    db_query($third_query);
  }
}