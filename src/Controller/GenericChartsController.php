<?php
/**
 * Generic_Charts_Controller grabs the input being passed from the 
 * GenericGraphForm and then for each series being requested We generate an array
 * of dates and prices that is then passed back to the Energy_Tracker_Controller 
 * to be passed into the render array So that the chart library can draw the 
 * appropriate graph
 *
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/30/2016
 * Time: 10:26 AM
 */

namespace Drupal\pps_energy_tracker\Controller;

use Drupal\Core\Database\Database;

class GenericChartsController {
  public $ON_PEAK_PRICES = array(array()); //On Peak Pricing Data
  public $OFF_PEAK_PRICES = array(array()); //Off Peak Pricing Data
  public $CHART_TYPE = 'NULL'; //Default Chart Type = ON PEAK
  public $TERM_START = null; //When the term starts for pricing
  public $TERM_END = null; //When the term ends for pricing
  public $PRICING_START = null; //Day that we will start the pricing on.
  public $MAX_DATE =  null; //Last day of pricing.
  public $ARRAY_DATE_KEYS = array(); //Need to change to 2D Array to handle multiple series
  public $MONTHLY_USAGE = 100000; //Declaration of the standard Monthly Usage as 100,000 kWh
  /**
   * Calls the functions in proper order to generate the [series][dates][prices] for the view
   *
   * @param $graph_choice - graph that needs to be queried Years 2015/2016/2017/2018/etc.
   * @param $graph_type - the type of graph that needs drawn On/Off/Mixed
   * @return string - return the pricing array
   */
  public function pricingController($graph_choice, $graph_type){
    //set all of the date and graph type constants
    $this->setTerms($graph_choice, $graph_type);

    //Grab the required data out of the on and off peak tables
    $pricingHolder = $this->queryData($graph_choice, $graph_type);
    $this->septemberFix();

    //Reset dates and calculate the price points to be graphed.
    $this->setTerms($graph_choice, $graph_type);
    $pricing = $this->pricingGeneration($pricingHolder, $graph_choice, $graph_type);

    //JSON Encode the data for it to be passed to the view
    $json_prices = $this->jsonEncode($pricing[0]);
    return $json_prices;
  }

  /**
   * Takes in the graph choice, and generates a query returning the data to the controller
   *
   * @param $graph_choice - graph that needs to be queried Years 2015/2016/2017/2018/etc.
   * @param $graph_type - the type of graph that needs drawn On/Off/Mixed
   * @return array - Return an array containing the queried data 0 - On Peak 1 - Off Peak
   */
  //TODO: Have this handle multiple series
  public function queryData($graph_choice, $graph_type){
    $con = Database::getConnection();
    $formattedStart = $this->PRICING_START->format('Y-m-d');
    $temp_array = array();
    $fields_array = array();
    $fields_array[] = 'purchase_date';

    do{
      //TODO: Set Array Date keys correctly here to avoid doing it later in the loop
      if($this->TERM_START->format('M') == 'Sep'){
        $fields_array[] = 'Sept_' . $this->TERM_START->format('y');
      }
      else{
        $fields_array[] = $this->TERM_START->format('M_y');
      }
      $this->ARRAY_DATE_KEYS[] = $this->TERM_START->format('M_y');
      $this->TERM_START->add(new \DateInterval('P1M'));
    }while($this->TERM_START < $this->TERM_END);
    
    $this->setTerms($graph_choice, $graph_type);


    if($graph_type == 'On Peak'){
      $query = $con->select('ppsweb_pricemodel.elec_on_peak', 'x')
        ->fields('x', $fields_array)
        ->orderBy('purchase_date', 'ASC')
        ->condition('purchase_date', $formattedStart, '>');
      $data = $query->execute();
      $temp_array[0] = $data->fetchAllAssoc('purchase_date');
    }

    else if($graph_type == 'Off Peak'){
      $query = $con->select('ppsweb_pricemodel.elec_off_peak', 'x')
        ->fields('x', $fields_array)
        ->orderBy('purchase_date', 'ASC')
        ->condition('purchase_date', $formattedStart, '>');
      $data = $query->execute();
      $temp_array[0] = $data->fetchAllAssoc('purchase_date');
    }

    else{
      $query = $con->select('ppsweb_pricemodel.elec_on_peak', 'x')
        ->fields('x', $fields_array)
        ->orderBy('purchase_date', 'ASC')
        ->condition('purchase_date', $formattedStart, '>');
      $data = $query->execute();
      $temp_array[0] = $data->fetchAllAssoc('purchase_date');

      $query = $con->select('ppsweb_pricemodel.elec_off_peak', 'x')
        ->fields('x', $fields_array)
        ->orderBy('purchase_date', 'ASC')
        ->condition('purchase_date', $formattedStart, '>');
      $data = $query->execute();
      $temp_array[1] = $data->fetchAllAssoc('purchase_date');
    }

    return $temp_array;
  }

  /**
   * Generates the requested pricing points and returns them to the view
   *
   * @param $data_array - queried array returned from queryData
   * @param $graph_choice - The years the graph pertains to -> '2015', '2016', '2015, 2016, 2017'
   * @param $graph_type - Type of graph -> 'On Peak', 'Off Peak', 'Mixed'
   * @return string - placeholder return documentation
   */
  public function pricingGeneration($data_array, $graph_choice, $graph_type){
    $temp_array = array(array(array()));
    $monthly_on_total = 0;
    $monthly_off_total = 0;

    //TODO: Once a multi-series argument is passed change the '1' to a variable
    //Run the loop one to three times depending upon the request
    for($i=0; $i<1; $i++){
      $zero_counter = 0;
      $current_keys = array_keys($data_array[$i]);
      $this->MAX_DATE = new \DateTime($current_keys[sizeof($current_keys) - 3]);

      //do/while loop runs from PRICING_START until MAX_DATE
      // incrementing one day at a time
      do{
        $current_day_formatted = $this->PRICING_START->format('Y-m-d');
        //Currently only setup to handle On or Off Peak pricing
        //TODO: Change this if statement to look at the graph_choice/graph_type variables so we can handle mixed pricing
        if(isset($data_array[$i][$current_day_formatted])){
          for($j=0; $j<12; $j++){
            $current_date_key = $this->ARRAY_DATE_KEYS[$j];
            $current_price = $data_array[$i][$current_day_formatted]
              ->$current_date_key;
            if($current_price > 0.0001){
              $monthly_on_total += (($current_price / 1000) * $this->MONTHLY_USAGE);
            }
            else{
              //Keep track of any 0's being returned from database
              $zero_counter++;
            }
          }
        }

        //TODO: Handle mixed On/Off Peak Pricing
        if($monthly_on_total > 0.00001 && $zero_counter < 12){
          /**
           * Calculate the price for today and add it to the pricing list
           * Price = (Term Cost / (Term Vol - (Num Zeroes * 100K ))
           */
          $price = ($monthly_on_total + $monthly_off_total) / 
            (($this->MONTHLY_USAGE * 12) - ($zero_counter * $this->MONTHLY_USAGE));
          //Array setup -> $temp_array[For Loop(Series#)[Date][Price]
          $temp_array[$i][$this->PRICING_START->format('Y-m-d')][] = $price;
        }
        else{
          /**
           * We have 12 0's for our prices, signifying that no pricing has been
           * found for today So we are going to just skip today and move on.
           */
        }
        //Flush variables for the next day of pricing
        $monthly_on_total = 0;
        $monthly_off_total = 0;
        $zero_counter = 0;
        $this->PRICING_START->add(new \DateInterval('P1D'));
      }while($this->PRICING_START < $this->MAX_DATE);
    }

    //Always seem to have an empty first record so lets unset that
    unset($temp_array[0][0]);

    return $temp_array;
  }

  /**
   * Replace september's abbreviation to match data model
   */
  public function septemberFix(){
    for($i=0; $i<sizeof($this->ARRAY_DATE_KEYS); $i++){
        $this->ARRAY_DATE_KEYS[$i] = str_replace('Sep', 'Sept', 
          $this->ARRAY_DATE_KEYS[$i]);
    }
  }

  /**
   * Reset the date constants
   *
   * @param $graph_choice - graph name ex: '2015', '2016', etc
   * @param $graph_type - On/Off Peak, Mixed
   */
  public function setTerms($graph_choice, $graph_type){
    /**
     * Graph Choice can be: 2015, 2016, 2017, 2018, 2019, (2015, 2016, 2017), 
     *   (2016, 2017, 2018)
     * Graph Type can be: On Peak, Off Peak, Mixed
     */
    //Set the term
    if($graph_choice == '2015'){
      $this->TERM_START = new \DateTime('2015-01-01');
      $this->TERM_END = new \DateTime('2015-12-31');
    }
    else if($graph_choice == '2016'){
      $this->TERM_START = new \DateTime('2016-01-01');
      $this->TERM_END = new \DateTime('2016-12-31');
    }
    else if($graph_choice == '2017'){
      $this->TERM_START = new \DateTime('2017-01-01');
      $this->TERM_END = new \DateTime('2017-12-31');
    }
    else if($graph_choice == '2018'){
      $this->TERM_START = new \DateTime('2018-01-01');
      $this->TERM_END = new \DateTime('2018-12-31');
    }
    else if($graph_choice == '2019'){
      $this->TERM_START = new \DateTime('2019-01-01');
      $this->TERM_END = new \DateTime('2019-12-31');
    }
    //TODO: Add special handling here for multiple series below
    else if($graph_choice == '2015, 2016, 2017'){

    }
    else if($graph_choice == '2016, 2017, 2018'){

    }
    //Set the pricing start -> This was hardcoded in the Java web app version
    $this->PRICING_START = new \DateTime('2013-03-01');
    $this->CHART_TYPE = $graph_type;
  }

  /**
   * Encode the 2D Array into a valid JSON result
   *
   * @param $temp_array - passed in multi-dimensional price array Dates/Prices
   * @return array - passed back out array of json encoded Dates/Prices
   */
  //TODO: Add multi-series support here
  public function jsonEncode($temp_array){
    /**$json_array = array();

    foreach($temp_array as $row){
      $json_array[] = json_encode($row);
    }**/
    return json_encode($temp_array);
  }
}