<?php
/**
 * Handles creating an array of dates and prices for an Electricity Account.
 * Returns an array of dates/prices.
 *
 * Created by PhpStorm.
 * User: Alex
 * Date: 6/14/2016
 * Time: 8:33 AM
 */

namespace Drupal\pps_energy_tracker\Controller;

use Drupal\Core\Database\Database;
use Drupal\pps_energy_tracker\Entity\Account;
use Drupal\pps_energy_tracker\Entity\AccountUsage;

class ElectricityChartsController {
  public $ON_PEAK_PRICES = array(array()); //On Peak Pricing Data
  public $OFF_PEAK_PRICES = array(array()); //Off Peak Pricing Data
  public $ARRAY_DATE_KEYS = array(); //Months we are working with in the format MMM_YYYY
  public $CHART_TYPE = 'NULL'; //Default Chart Type = ON PEAK
  public $TERM_START = null; //When the term starts for pricing
  public $TERM_END = null; //When the term ends for pricing
  public $PRICING_START = null; //Day that we will start the pricing on.
  public $MAX_DATE =  null; //Last day of pricing.

  public $SMALLEST_DATE = null; //Global constant used for query
  public $HIGHEST_DATE = null; //Global constant used for query

  /**
   * Controller class for Electricity Charts.
   * Receives an 'account id' from the view, Calls the necessary functions, and returns an array of Dates/Prices to the view.
   *
   * @param $account_id -> account id
   * @return array -> return the price/date array
   */
  public function pricingController($account_id){
    $output = array();
    //Pull Account Information
    $account = $this->pullAccountData($account_id);

    //Set the terms
    $this->setTerms($account, false, 1);

    //Query the data
    $pricingHolder = $this->queryData($account);

    //Reset the terms
    $this->setTerms($account, false, 1);

    //Calculate On and Off Peak Volumes


    foreach($account[2] as $iteration){
      //Run the pricing algorithm
      $this->setTerms($account, false, $iteration);
      $peaks = $this->calculatePeakNumbers($account, $iteration);
      $this->setTerms($account, false, $iteration);
      $final_data = $this->pricingGeneration($pricingHolder, $account, $peaks, $iteration);

      //Set the last date and price on the account and update
      $this->updateAccount($account[0], $final_data);
      $output[] = $final_data;
    }


    //JSON encode the data and pass back to the frontend
    $output = $this->jsonEncode($output);
    return $output;
  }

  /**
   * Grab the account data and pass it to the Account/Account Usage constructors
   *
   * @param $account_id -> The ID of the account we are working with
   * @return array -> Return and array with the account and usage
   */
  public function pullAccountData($account_id){
    $con = Database::getConnection();
    $active_series = array(); //Tell us which series have a complete start/end
    $active_series[] = 1;
    $datesToBeSorted = array();

    $query = $con->select('ppsweb_pricemodel.account', 'x')
      ->fields('x')
      ->condition('id', $account_id, '=');
    $data = $query->execute();
    $temp_account = $data->fetchAll();
    
    $query = $con->select('ppsweb_pricemodel.account_usage', 'x')
      ->fields('x')
      ->condition('id', $temp_account[0]->usage_id, '=');
    $data = $query->execute();
    $temp_usage = $data->fetchAll();

    $account = new Account($temp_account[0]);
    $account_usage = new AccountUsage($temp_usage[0]);

    //Grab and set Utility Name; this should be moved into a JOIN at some point
    $query = $con->select('ppsweb_pricemodel.utility', 'x')
      ->fields('x', array('utility_name'))
      ->condition('utility_id', array($account->getUtilityId()));
    $data = $query->execute();
    $temp = $data->fetchAll();

    //Set the utility we are working with
    $account->setUtilityName($temp[0]->utility_name);

    //Log how many iterations we are going for and set the Highest/Smallest dates
    if($account->getContractStart2() != null && $account->getContractEnd2() != null){
      $active_series[] = 2;
    }
    if($account->getContractStart3() != null && $account->getContractEnd3() != null){
      $active_series[] = 3;
    }
    $this->resetBarriers($account);

    //Return our data
    return[$account, $account_usage, $active_series];
  }

  /**
   * Query up all of the relevant pricing data.
   *
   * @return array -> Return array holding all of the queried data KEYS:[0]->Off Peak [1]->On Peak [2]->Capacity
   */
  //TODO: Have this handle multiple series
  public function queryData($account){
    $con = Database::getConnection();
    $fields_array = array();
    $temp_array = array();
    $formattedStart = $this->PRICING_START->format('Y-m-d');
    $fields_array[] = 'purchase_date';

    //gather the columns in the necessary format
    do{
      //TODO: Set Array Date keys correctly here to avoid doing it later in the loop
      if($this->SMALLEST_DATE->format('M') == 'Sep'){
        $fields_array[] = 'Sept_' . $this->SMALLEST_DATE->format('y');
      }
      else{
        $fields_array[] = $this->SMALLEST_DATE->format('M_y');
      }
      $this->ARRAY_DATE_KEYS[] = $this->SMALLEST_DATE->format('M_y');
      $this->SMALLEST_DATE->add(new \DateInterval('P1M'));
    }while($this->SMALLEST_DATE <= $this->HIGHEST_DATE);

    //Construct and run the queries
    $query = $con->select('ppsweb_pricemodel.elec_off_peak', 'x')
      ->fields('x', $fields_array)
      ->orderBy('purchase_date', 'ASC')
      ->condition('purchase_date', $formattedStart, '>');
    $data = $query->execute();
    $temp_array[0] = $data->fetchAllAssoc('purchase_date');

    $query = $con->select('ppsweb_pricemodel.elec_on_peak', 'x')
      ->fields('x', $fields_array)
      ->orderBy('purchase_date', 'ASC')
      ->condition('purchase_date', $formattedStart, '>');
    $data = $query->execute();
    $temp_array[1] = $data->fetchAllAssoc('purchase_date');

    $fields_array[0] = 'utility_name';
    //TODO: Only query the requested utility
    $query = $con->select('ppsweb_pricemodel.elec_capacity', 'x')
      ->fields('x', $fields_array)
      ->condition('utility_name', $account[0]->getUtilityName());
    $data = $query->execute();
    $temp_array[2] = $data->fetchAllAssoc('utility_name');

    return $temp_array;
  }

  /**
   * Calculate On and Off Peak Volumes
   *
   * @param $account -> Contains [0]Account and [1]Account Usage Objects
   * @return array -> Returns array containing the [0]On and [1]Off Peak Numbers [2] Termvol
   */
  public function calculatePeakNumbers($account, $iteration){
    $on_peak = array();
    $off_peak = array();
    $termVol = 0.0;

    do{
      if($this->TERM_START->format('M') == 'Jan'){
        $termVol += $account[1]->getJanUsage();
      }
      if($this->TERM_START->format('M') == 'Feb'){
        $termVol += $account[1]->getFebUsage();
      }
      if($this->TERM_START->format('M') == 'Mar'){
        $termVol += $account[1]->getMarUsage();
      }
      if($this->TERM_START->format('M') == 'Apr'){
        $termVol += $account[1]->getAprUsage();
      }
      if($this->TERM_START->format('M') == 'May'){
        $termVol += $account[1]->getMayUsage();
      }
      if($this->TERM_START->format('M') == 'Jun'){
        $termVol += $account[1]->getJunUsage();
      }
      if($this->TERM_START->format('M') == 'Jul'){
        $termVol += $account[1]->getJulUsage();
      }
      if($this->TERM_START->format('M') == 'Aug'){
        $termVol += $account[1]->getAugUsage();
      }
      if($this->TERM_START->format('M') == 'Sept'){
        $termVol += $account[1]->getSeptUsage();
      }
      if($this->TERM_START->format('M') == 'Oct'){
        $termVol += $account[1]->getOctUsage();
      }
      if($this->TERM_START->format('M') == 'Nov'){
        $termVol += $account[1]->getNovUsage();
      }
      if($this->TERM_START->format('M') == 'Dec'){
        $termVol += $account[1]->getDecUsage();
      }
      $this->TERM_START->add(new \DateInterval('P1M'));
    }while($this->TERM_START < $this->TERM_END);

    //On Peak Volume -> MMM_Usage * (MMM_On_Peak/100)
    $on_peak['Jan'] = $account[1]->getJanUsage() * ($account[1]->getJanOnPeak() / 100);
    $on_peak['Feb'] = $account[1]->getFebUsage() * ($account[1]->getFebOnPeak() / 100);
    $on_peak['Mar'] = $account[1]->getMarUsage() * ($account[1]->getMarOnPeak() / 100);
    $on_peak['Apr'] = $account[1]->getAprUsage() * ($account[1]->getAprOnPeak() / 100);
    $on_peak['May'] = $account[1]->getMayUsage() * ($account[1]->getMayOnPeak() / 100);
    $on_peak['Jun'] = $account[1]->getJunUsage() * ($account[1]->getJunOnPeak() / 100);
    $on_peak['Jul'] = $account[1]->getJulUsage() * ($account[1]->getJulOnPeak() / 100);
    $on_peak['Aug'] = $account[1]->getAugUsage() * ($account[1]->getAugOnPeak() / 100);
    $on_peak['Sept'] = $account[1]->getSeptUsage() * ($account[1]->getSeptOnPeak() / 100);
    $on_peak['Oct'] = $account[1]->getOctUsage() * ($account[1]->getOctOnPeak() / 100);
    $on_peak['Nov'] = $account[1]->getNovUsage() * ($account[1]->getNovOnPeak() / 100);
    $on_peak['Dec'] = $account[1]->getDecUsage() * ($account[1]->getDecOnPeak() / 100);

    //Off Peak Volume -> MMM_Usage - MMM_On Peak Volume
    $off_peak['Jan'] = $account[1]->getJanUsage() - $on_peak['Jan'];
    $off_peak['Feb'] = $account[1]->getFebUsage() - $on_peak['Feb'];
    $off_peak['Mar'] = $account[1]->getMarUsage() - $on_peak['Mar'];
    $off_peak['Apr'] = $account[1]->getAprUsage() - $on_peak['Apr'];
    $off_peak['May'] = $account[1]->getMayUsage() - $on_peak['May'];
    $off_peak['Jun'] = $account[1]->getJunUsage() - $on_peak['Jun'];
    $off_peak['Jul'] = $account[1]->getJulUsage() - $on_peak['Jul'];
    $off_peak['Aug'] = $account[1]->getAugUsage() - $on_peak['Aug'];
    $off_peak['Sept'] = $account[1]->getSeptUsage() - $on_peak['Sept'];
    $off_peak['Oct'] = $account[1]->getOctUsage() - $on_peak['Oct'];
    $off_peak['Nov'] = $account[1]->getNovUsage() - $on_peak['Nov'];
    $off_peak['Dec'] = $account[1]->getDecUsage() - $on_peak['Dec'];

    return [$on_peak, $off_peak, $termVol];
  }

  /**
   * Generate the pricing array for the view
   *
   * @param $dataArray [0]->Off Peak [1]->On Peak [2]->Capacity
   * @param $account [0]->Account Object [1]->AccountUsage object [2] -> Active Series
   * @param $peakNumbers [0]->On Peak Numbers [1]->Off Peak Numbers [2]->TermVol
   * @return array Return an array of Dates/Prices
   */
  //TODO: Handle multiple series max of '3'
  public function pricingGeneration($dataArray, $account, $peakNumbers, $iteration){
    $temp_array = array(array());
    $term_vol = $peakNumbers[2];
    $utility_key = $account[0]->getUtilityName();
    $utility_key = str_replace(" ", "", $utility_key);
    /**
     * Loop from PRICING_START to MAX_DATE
     * Increment 1 day at a time adding the calculated prices to an array
     */
    do{
      /**
       * Loop from TERM_START to TERM_END
       * Calculate the price point for today
       *
       * 1: total the OnPeak/OffPeak/Capacity for the term on todays date
       * 1A: On/Off Peak values from the database are divided by 1,000 then multiplied by their corresponding usage
       * 2: Todays price is then calculate by summing On/Off/Capacity and (Addtl costs multiplied by the term vol)
       * 2A: TotalOn + TotalOff + TotalCap + (AdtlCost * Term Volume)
       * 2B: Take that grand total and divide it by the Term Volume
       * 3: Add that price to the array of [Date]->Price
       */
      $totalOn=0;
      $totalOff=0;
      $totalCap=0;
      $empty = false;
      $arrayFormat = $this->PRICING_START->format('Y-m-d');
      //Prep Capacity by converting to an associative array
      //TODO:Re-Evaluate this usage, I don't think it should be necessary
      $capArray = json_decode(json_encode($dataArray[2]), true);

      do{
        //Format strings into our array key format
        $monthString = $this->TERM_START->format('M');
        $capFormat = $this->TERM_START->format('M_y');

        //September exception
        if($monthString == 'Sep'){
          $monthString = 'Sept';
          $capFormat = str_replace('Sep', 'Sept', $capFormat);
        }

        //Total On/Off Peak Numbers as well as Capacity
        if(isset($dataArray[0][$arrayFormat]->$capFormat) && $dataArray[0][$arrayFormat]->$capFormat != null){
          $totalOn += ($dataArray[0][$arrayFormat]->$capFormat / 1000) * $peakNumbers[0][$monthString];
          $totalOff += ($dataArray[1][$arrayFormat]->$capFormat / 1000) * $peakNumbers[1][$monthString];
          $totalCap += $capArray[$account[0]->getUtilityName()][$capFormat];
        }
        //no data found in the arrays for today break and head to the next day
        else{
          $empty=true;
          break;
        }

        //Increment by one month
        $this->TERM_START->add(new \DateInterval('P1M'));
      }while($this->TERM_START < $this->TERM_END);

      //if we have data toss it into the array. otherwise continue on
      if($empty == false){
        $price = ($totalOn + $totalOff + $totalCap + ($account[1]->getAdtlCost() * $term_vol)) / $term_vol;
        $temp_array[$this->PRICING_START->format('Y-m-d')][] = $price;
      }

      //reset TERM Variables and move to the next day
      $this->setTerms($account, true, $iteration);
      $this->PRICING_START->add(new \DateInterval('P1D'));
    }while($this->PRICING_START < $this->MAX_DATE);
    unset($temp_array[0]);
    return $temp_array;
  }

  /**
   * Set/Reset the term variables
   *
   * @param $account array containing the account/account usage objects Array Keys: [0]->Account [1]->Account Usage
   * @param $skip boolean value that tells us whether or not a full reset
   * @param $iteration -> value indicating which iteration of the price generating loop we are on
   */
  public function setTerms($account, $skip, $iteration){
    if($skip != true){
      $this->PRICING_START = new \DateTime($account[0]->getPricingStart());
    }
    if($iteration == 1){
      $this->TERM_START = new \DateTime($account[0]->getContractStart());
      $this->TERM_END = new \DateTime($account[0]->getContractEnd());
      $this->MAX_DATE = new \DateTime($account[0]->getContractStart());
    }
    elseif($iteration == 2){
      $this->TERM_START = new \DateTime($account[0]->getContractStart2());
      $this->TERM_END = new \DateTime($account[0]->getContractEnd2());
      $this->MAX_DATE = new \DateTime($account[0]->getContractStart2());
    }
    else{
      $this->TERM_START = new \DateTime($account[0]->getContractStart3());
      $this->TERM_END = new \DateTime($account[0]->getContractEnd3());
      $this->MAX_DATE = new \DateTime($account[0]->getContractStart3());
    }
  }

  /**
   * JSON encode the pricing array and pass it back to the view
   *
   * @param $temp_array -> Array of Dates/Prices
   * @return string -> Returns JSON encoded Dates/Prices array
   */
  public function jsonEncode( $temp_array ){
    return json_encode($temp_array);
  }

  /**
   * Update the Account in the database with Last Date/price info
   *
   * @param $account -> Account we are looking to update
   * @param $finalData -> Pricing Data we've generated
   */
  public function updateAccount($account, $finalData){
    $con = Database::getConnection();
    end($finalData);
    $final_date = key($finalData);
    $final_price = $finalData[key($finalData)][0];
    if($final_date == null || $final_price == null){
      $final_date = '1999-01-01';
      $final_price = '0.00';
    }
    $query = $con->update('ppsweb_pricemodel.account')
      ->fields(array(
        'last_price' => $final_price,
        'last_date' => $final_date))
      ->condition('id', $account->getId(), '=');
    $query->execute();
  }

  /**
   * Reset the Highest/Smallest Dates
   *
   * @param $account
   */
  public function resetBarriers($account){
    //Log how many iterations we are going for and set the Highest/Smallest dates
    $s = new \DateTime($account->getContractStart());
    $e = new \DateTime($account->getContractEnd());
    $datesToBeSorted[] =  $s;
    $datesToBeSorted[] =  $e;
    if($account->getContractStart2() != null && $account->getContractEnd2() != null){
      $s = new \DateTime($account->getContractStart2());
      $e = new \DateTime($account->getContractEnd2());
      $datesToBeSorted[] =  $s;
      $datesToBeSorted[] =  $e;
    }
    if($account->getContractStart3() != null && $account->getContractEnd3() != null){
      $s = new \DateTime($account->getContractStart3());
      $e = new \DateTime($account->getContractEnd3());
      $datesToBeSorted[] =  $s;
      $datesToBeSorted[] =  $e;
    }

    //Set our constants for querying the the right months/years
    $this->SMALLEST_DATE = min($datesToBeSorted);
    $this->HIGHEST_DATE = max($datesToBeSorted);
  }
}