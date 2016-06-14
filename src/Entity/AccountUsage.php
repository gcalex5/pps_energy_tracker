<?php
/**
 * Account Usage Class
 * 
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/30/2016
 * Time: 10:17 AM
 */

namespace Drupal\pps_energy_tracker\Entity;

class AccountUsage {
  
  protected $id;
  protected $adtl_cost;
  protected $usage_id;
  protected $cap_obligation;
  protected $on_peak_percent;
  protected $off_peak_percent;
  protected $jan_usage;
  protected $feb_usage;
  protected $mar_usage;
  protected $apr_usage;
  protected $may_usage;
  protected $jun_usage;
  protected $jul_usage;
  protected $aug_usage;
  protected $sept_usage;
  protected $oct_usage;
  protected $nov_usage;
  protected $dec_usage;
  protected $jan_on_peak;
  protected $feb_on_peak;
  protected $mar_on_peak;
  protected $apr_on_peak;
  protected $may_on_peak;
  protected $jun_on_peak;
  protected $jul_on_peak;
  protected $aug_on_peak;
  protected $sept_on_peak;
  protected $oct_on_peak;
  protected $nov_on_peak;
  protected $dec_on_peak;

  public function __construct($dbData){
    $this->id = $dbData->id;
    $this->adtl_cost = $dbData->adtl_cost;
    $this->usage_id = $dbData->usage_id;
    $this->cap_obligation = $dbData->cap_obligation;
    $this->on_peak_percent = $dbData->on_peak_percent;
    $this->off_peak_percent = $dbData->off_peak_percent;
    $this->jan_usage = $dbData->jan_usage;
    $this->feb_usage = $dbData->feb_usage;
    $this->mar_usage = $dbData->mar_usage;
    $this->apr_usage = $dbData->apr_usage;
    $this->may_usage = $dbData->may_usage;
    $this->jun_usage = $dbData->jun_usage;
    $this->jul_usage = $dbData->jul_usage;
    $this->aug_usage = $dbData->aug_usage;
    $this->sept_usage = $dbData->sept_usage;
    $this->oct_usage = $dbData->oct_usage;
    $this->nov_usage = $dbData->nov_usage;
    $this->dec_usage = $dbData->dec_usage;
    $this->jan_on_peak = $dbData->jan_on_peak;
    $this->feb_on_peak = $dbData->feb_on_peak;
    $this->mar_on_peak = $dbData->mar_on_peak;
    $this->apr_on_peak = $dbData->apr_on_peak;
    $this->may_on_peak = $dbData->may_on_peak;
    $this->jun_on_peak = $dbData->jun_on_peak;
    $this->jul_on_peak = $dbData->jul_on_peak;
    $this->aug_on_peak = $dbData->aug_on_peak;
    $this->sept_on_peak = $dbData->sept_on_peak;
    $this->oct_on_peak = $dbData->oct_on_peak;
    $this->nov_on_peak = $dbData->nov_on_peak;
    $this->dec_on_peak = $dbData->dec_on_peak;
  }


}