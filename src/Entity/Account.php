<?php
/**
 * Account Class
 * 
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/30/2016
 * Time: 10:17 AM
 */

namespace Drupal\pps_energy_tracker\Entity;

class Account {
  
  protected $id;
  protected $user_id;
  protected $usage_id;
  protected $business_name;
  protected $pricing_start;
  protected $contract_start;
  protected $contract_end;
  protected $contract_start_2;
  protected $contract_end_2;
  protected $contract_start_3;
  protected $contract_end_3;
  protected $target_price;
  protected $last_price;
  protected $last_price_2;
  protected $last_price_3;
  protected $last_date;
  protected $last_date_2;
  protected $last_date_3;
  
  public function __construct($dbData){
    $this->id = $dbData->id;
    $this->usage_id = $dbData->id;
    $this->business_name = $dbData->business_name;
    $this->pricing_start = $dbData->pricing_start;
    $this->contract_start = $dbData->contract_start;
    $this->contract_end = $dbData->contract_end;
    $this->contract_start_2 = $dbData->contract_start_2;
    $this->contract_end_2 = $dbData->contract_end_2;
    $this->contract_start_3 = $dbData->contract_start_3;
    $this->contract_end_3 = $dbData->contract_end_3;
    $this->target_price = $dbData->target_price;
    $this->last_price = $dbData->last_price;
    $this->last_price_2 = $dbData->last_price_2;
    $this->last_price_3 = $dbData->last_price_3;
    $this->last_date = $dbData->last_date;
    $this->last_date_2 = $dbData->last_date_2;
    $this->last_date_3 = $dbData->last_date_3;
  }
}