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
    /**
     * Personal Info
     */
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
      '#value' => ['A', 'B', 'C'],
      '#required' => TRUE,
    );
    $form['pricing_start'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Pricing Start'),
      '#size' => 20,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    //TODO: Need to support 3 Contract Start/End's
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

    /**
     * Monthly Usage
     * There really needs to be a better way to do this
     */
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

    /**
     * On/Off Peak
     */
    //TODO: Handle all 12 mont
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
    //TODO: Store form data in the database
    /**
     * Database Notes
     * account.id = Auto-Increment
     * account.user_id -> Does Drupal have a user id?
     * account.usage_id -> Reference
     * account.utility_id -> Reference
     * account.last_date = needs set to a date as a placeholder
     *
     * account_usage.id = Auto-Increment
     * INT(11)
     * account_usage.usage_id
     * account_usage.xxx_usage -> All 12 months
     * account_usage.xxx_demand -> All 12 months. Not used anymore?
     * account_usage.xxx_on_peak -> All 12 months.
     *
     */
    $bus_name = $form_state->getValue('bus_name');
    $utility = $form['utility']['#options'][$form_state->getValue('utility')];
    $pricing_start = $form_state->getValue('pricing_start');
    $contract_s_1 = $form_state->getValue('contract_s_1');
    $contract_e_1 = $form_state->getValue('contract_e_1');
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

    $query = "INSERT INTO ppsweb_pricemodel.account (user_id, utility_id, usage_id, business_name, pricing_start, ".
      "contract_start, contract_end, trarget_price, last_price, last_date) ";
    $query .= "(0, 0, 0, " . $bus_name .", ". $pricing_start .", " . $contract_s_1 .", ". $contract_e_1 . ", ". $target_price . ", 0, ". "2016-06-25";

    db_query($query);
  }
}