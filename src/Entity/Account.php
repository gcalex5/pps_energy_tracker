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
  protected $utility_id;
  protected $utility_name;
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
    $this->utility_id = $dbData->utility_id;
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

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return mixed
   */
  public function getUserId()
  {
    return $this->user_id;
  }

  /**
   * @return mixed
   */
  public function getUtilityId()
  {
    return $this->utility_id;
  }

  /**
   * @return mixed
   */
  public function getUtilityName()
  {
    return $this->utility_name;
  }

  /**
   * @param mixed $utility_name
   */
  public function setUtilityName($utility_name)
  {
    $this->utility_name = $utility_name;
  }
  
  /**
   * @return mixed
   */
  public function getUsageId()
  {
    return $this->usage_id;
  }

  /**
   * @return mixed
   */
  public function getBusinessName()
  {
    return $this->business_name;
  }

  /**
   * @return mixed
   */
  public function getPricingStart()
  {
    return $this->pricing_start;
  }

  /**
   * @return mixed
   */
  public function getContractStart()
  {
    return $this->contract_start;
  }

  /**
   * @return mixed
   */
  public function getContractEnd()
  {
    return $this->contract_end;
  }

  /**
   * @return mixed
   */
  public function getContractStart2()
  {
    return $this->contract_start_2;
  }

  /**
   * @return mixed
   */
  public function getContractEnd2()
  {
    return $this->contract_end_2;
  }

  /**
   * @return mixed
   */
  public function getContractStart3()
  {
    return $this->contract_start_3;
  }

  /**
   * @return mixed
   */
  public function getContractEnd3()
  {
    return $this->contract_end_3;
  }

  /**
   * @return mixed
   */
  public function getTargetPrice()
  {
    return $this->target_price;
  }

  /**
   * @return mixed
   */
  public function getLastPrice()
  {
    return $this->last_price;
  }

  /**
   * @return mixed
   */
  public function getLastPrice2()
  {
    return $this->last_price_2;
  }

  /**
   * @return mixed
   */
  public function getLastPrice3()
  {
    return $this->last_price_3;
  }

  /**
   * @return mixed
   */
  public function getLastDate()
  {
    return $this->last_date;
  }

  /**
   * @return mixed
   */
  public function getLastDate2()
  {
    return $this->last_date_2;
  }

  /**
   * @return mixed
   */
  public function getLastDate3()
  {
    return $this->last_date_3;
  }
}