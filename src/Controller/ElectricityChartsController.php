<?php
/**
 *
 *
 * Created by PhpStorm.
 * User: Alex
 * Date: 6/14/2016
 * Time: 8:33 AM
 */
//TODO: documentation

namespace Drupal\pps_energy_tracker\Controller;

class ElectricityChartsController {
  public $ON_PEAK_PRICES = array(array()); //On Peak Pricing Data
  public $OFF_PEAK_PRICES = array(array()); //Off Peak Pricing Data
  public $ARRAY_DATE_KEYS = array();
  public $CHART_TYPE = 'NULL'; //Default Chart Type = ON PEAK
  public $TERM_START = null; //When the term starts for pricing
  public $TERM_END = null; //When the term ends for pricing
  public $PRICING_START = null; //Day that we will start the pricing on.
  public $MAX_DATE =  null; //Last day of pricing.

  /**
   *
   * @param $account_id -> account id
   * @return array -> return the price/date array
   */
  public function pricingController( $account_id ){
    $pricingHolder = $this->queryData($account_id);

    return $this->jsonEncode($pricingHolder);
  }

  /**
   * Query up all of the relevant pricing data.
   *
   * @param $account_id->the ID of the 'Account' we are working with. Not needed?
   * @return array
   */
  //TODO: Have this handle multiple series
  public function queryData( $account_id ){
    $temp_array = array();
    $offPeakQuery = "SELECT purchase_date, ";

    //gather the columns in the necessary format
    do{
      $offPeakQuery = $offPeakQuery . $this->TERM_START->format('M_y, ');
      $this->ARRAY_DATE_KEYS[] = $this->TERM_START->format('M_y');
      $this->TERM_START->add(new \DateInterval('P1M'));
    }while($this->TERM_START <= $this->TERM_END);

    //Construct the queries
    $formattedStart = $this->PRICING_START->format('Y-m-d');
    $offPeakQuery = str_replace('Sep', 'Sept', $offPeakQuery);
    $onPeakQuery = $offPeakQuery . 'FROM ppsweb_pricemodel.elec_on_peak WHERE purchase_date > ' . $formattedStart;
    $capacityQuery = $offPeakQuery . 'FROM ppsweb_pricemodel.elec_capacity WHERE purchase_date > ' . $formattedStart;
    //Is $dateQuery really needed?
    $dateQuery = $offPeakQuery . 'FROM ppsweb_pricemodel.elec_on_peak WHERE purchase_date > ' . $formattedStart;
    $offPeakQuery .= 'FROM ppsweb_pricemodel.elec_off_peak WHERE purchase_date > ' . $formattedStart;

    //Query the data
    $temp_array[0] = db_query($offPeakQuery)->fetchAllAssoc('purchase_date');
    $temp_array[1] = db_query($onPeakQuery)->fetchAllAssoc('purchase_date');
    $temp_array[2] = db_query($capacityQuery)->fetchAllAssoc('purchase_date');
    //Not sure if this is needed anymore
    $temp_array[3] = db_query($dateQuery)->fetchAllAssoc('purchase_date');

    return $temp_array;
  }

  /**
   * Generate the pricing array for the view
   *
   * @param $dataArray [0]->Off Peak [1]->On Peak [2]->Capacity [3]->Dates?
   * @return array
   */
  public function pricingGeneration( $dataArray ){
    $temp_array = array(array());
    $on_peak = array();
    $off_peak = array();

    //Calculate on and off peak volumes per month over the course of the contract
    do{
      //On Peak Volume -> MMM_Usage * (MMM_On_Peak/100)
      //Off Peak Volume -> MMM_Usage - MMM_On Peak Volume
      $this->TERM_START->add(new \DateInterval('P1M'));
    }while($this->TERM_START < $this->TERM_END);

    //Calculate the pricing points from PRICING_START until MAX_DATE
    do{
      //Loop from TERM_START to TERM_END
      //Calculate the price point for today
      /**
       * Calculate the price point for today
       * 1: total the OnPeak/OffPeak/Capacity for the term on todays date
       * 1A: On/Off Peak values from the database are divided by 1,000 then multiplied by their corresponding usage
       * 2: Todays price is then calculate by summing On/Off/Capacity and (Addtl costs multiplied by the term vol)
       * 2A: TotalOn + TotalOff + TotalCap + (AdtlCost * Term Volume)
       * 2B: Take that grand total and divide it by the Term Volume
       * 3: Add that price to the array of [Date]->Price
       */
      do{

        $this->TERM_START->add(new \DateInterval('P1M'));
      }while($this->TERM_START<$this->TERM_END);

      //reset TERM_START
      $this->setTerms();

      $this->PRICING_START->add(new \DateInterval('P1D'));
    }while($this->PRICING_START < $this->MAX_DATE);





    return $temp_array;
  }

  /**
   *
   *
   */
  public function setTerms(){

  }

  /**
   *
   *
   * @param $temp_array
   * @return string
   */
  public function jsonEncode( $temp_array ){
    return json_encode($temp_array);
  }




}