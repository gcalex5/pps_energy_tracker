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
//TODO: documentation

namespace Drupal\pps_energy_tracker\Controller;

use Drupal\pps_energy_tracker\Entity\Account;
use Drupal\pps_energy_tracker\Entity\AccountUsage;
use Symfony\Component\Validator\Constraints\DateTime;

class ElectricityChartsController {
  public $ON_PEAK_PRICES = array(array()); //On Peak Pricing Data
  public $OFF_PEAK_PRICES = array(array()); //Off Peak Pricing Data
  public $ARRAY_DATE_KEYS = array(); //Months we are working with in the format MMM_YYYY
  public $CHART_TYPE = 'NULL'; //Default Chart Type = ON PEAK
  public $TERM_START = null; //When the term starts for pricing
  public $TERM_END = null; //When the term ends for pricing
  public $PRICING_START = null; //Day that we will start the pricing on.
  public $MAX_DATE =  null; //Last day of pricing.

  /**
   * Controller class for Electricity Charts.
   * Receives an 'account id' from the view, Calls the necessary functions, and returns an array of Dates/Prices to the view.
   *
   * @param $account_id -> account id
   * @return array -> return the price/date array
   */
  public function pricingController($account_id){
    //Pull Account Information
    $account = $this->pullAccountData($account_id);

    //Set the terms
    $this->setTerms($account, false);

    //Query the data
    $pricingHolder = $this->queryData($account);

    //Reset the terms
    $this->setTerms($account, false);

    //Calculate On and Off Peak Volumes
    $peaks = $this->calculatePeakNumbers($account);

    //Run the pricing algorithm
    $this->pricingGeneration($pricingHolder, $account, $peaks);

    //JSON encode the data and pass back to the frontend
    return $this->jsonEncode($pricingHolder);
  }

  /**
   * Grab the account data and pass it to the Account/Account Usage constructors
   *
   * @param $account_id -> The ID of the account we are working with
   * @return array -> Return and array with the account and usage
   */
  public function pullAccountData($account_id){
    $temp_account = db_query('SELECT * FROM ppsweb_pricemodel.account WHERE id=' . $account_id)->fetchAll();
    $temp_usage = db_query('SELECT * FROM ppsweb_pricemodel.account_usage WHERE id=' . $temp_account[0]->usage_id)->fetchAll();

    $account = new Account($temp_account[0]);
    $account_usage = new AccountUsage($temp_usage[0]);

    return[$account, $account_usage];
  }

  /**
   * Query up all of the relevant pricing data.
   *
   * @return array -> Return array holding all of the queried data KEYS:[0]->Off Peak [1]->On Peak [2]->Capacity
   */
  //TODO: Have this handle multiple series
  public function queryData(){
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
    //September abbreviation fix
    $offPeakQuery = str_replace('Sep', 'Sept', $offPeakQuery);
    //Remove the last comma
    $offPeakQuery = preg_replace('/,([^,]*)$/', ' \1', $offPeakQuery);
    //Append the other queries with the correct data
    $onPeakQuery = $offPeakQuery . "FROM ppsweb_pricemodel.elec_on_peak WHERE purchase_date > '" . $formattedStart . "' ORDER BY purchase_date";
    $capacityQuery = $offPeakQuery . "FROM ppsweb_pricemodel.elec_capacity";
    //Remove the purchase date field from the capacity query
    $capacityQuery = str_replace('purchase_date,', 'utility_name,', $capacityQuery);
    $offPeakQuery .= "FROM ppsweb_pricemodel.elec_off_peak WHERE purchase_date > '" . $formattedStart . "' ORDER BY purchase_date";

    //Query the data
    $temp_array[0] = db_query($offPeakQuery)->fetchAllAssoc('purchase_date');
    $temp_array[1] = db_query($onPeakQuery)->fetchAllAssoc('purchase_date');
    $temp_array[2] = db_query($capacityQuery)->fetchAllAssoc('purchase_date');

    return $temp_array;
  }

  /**
   * Calculate On and Off Peak Volumes
   *
   * @param $account -> Contains [0]Account and [1]Account Usage Objects
   * @return array -> Returns array containing the [0]On and [1]Off Peak Numbers
   */
  public function calculatePeakNumbers($account){
    $on_peak = array();
    $off_peak = array();

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

    return [$on_peak, $off_peak];
  }

  /**
   * Generate the pricing array for the view
   *
   * @param $dataArray [0]->Off Peak [1]->On Peak [2]->Capacity
   * @param $account [0]->Account Object [1]->AccountUsage object
   * @param $peakNumbers [0]->On Peak Numbers [1]->Off Peak Numbers
   * @return array Return an array of Dates/Prices
   */
  //TODO: Handle multiple series max of '3'
  public function pricingGeneration($dataArray, $account, $peakNumbers){
    $temp_array = array(array());
    $term_vol = array_sum($peakNumbers[0]) + array_sum($peakNumbers[1]);
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
      //Prep Capacity by converting to an associative array
      //TODO:Re-Evaluate this usage, I don't think it should be necessary
      $capArray = json_decode(json_encode($dataArray[2]), true);

      do{
        //Format string into our array key format
        $monthString = $this->TERM_START->format('M');
        $capFormat = $this->TERM_START->format('M_y');

        //September exception
        if($monthString == 'Sep'){
          $monthString = 'Sept';
          $capFormat = str_replace('Sep', 'Sept', $capFormat);
        }

        //Total On/Off Peak Numbers
        $totalOn += ($peakNumbers[0][$monthString] / 1000);
        $totalOff += ($peakNumbers[1][$monthString] / 1000);
        $totalCap += $capArray[''][$capFormat];

        //Increment by one month
        $this->TERM_START->add(new \DateInterval('P1M'));
      }while($this->TERM_START < $this->TERM_END);

      //Calculate today's price and store in the array to return
      $price = $totalOn + $totalOff + $totalCap + ($account[1]->getAdtlCost() * $term_vol);
      $temp_array[$this->PRICING_START->format('Y-M-d')][] = $price;

      //reset TERM Variables and move to the next day
      $this->setTerms($account, true);
      $this->PRICING_START->add(new \DateInterval('P1D'));
    }while($this->PRICING_START < $this->MAX_DATE);

    return $temp_array;
  }

  /**
   * Set/Reset the term variables
   *
   * @param $account array containing the account/account usage objects Array Keys: [0]->Account [1]->Account Usage
   * @param $skip boolean value that tells us whether or not a full reset
   */
  public function setTerms($account, $skip){
    if($skip != true){
      $this->PRICING_START = new \DateTime($account[0]->getPricingStart());
    }
    $this->TERM_START = new \DateTime($account[0]->getContractStart());
    $this->TERM_END = new \DateTime($account[0]->getContractEnd());
    $this->MAX_DATE = new \DateTime($account[0]->getContractStart());
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