<?php
//TODO: Add proper documentation here
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/30/2016
 * Time: 10:26 AM
 */

namespace Drupal\pps_energy_tracker\Controller;
use Drupal\Core\Render\Element\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class Generic_Charts_Controller {
  //TODO: Should these really initially by null?
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
   * ->Entry Point Of Class<-
   * Makes the initial function call to start calculating the pricing data.
   *
   * @param $graph_choice - graph that needs to be queried Years 2015/2016/2017/2018/etc.
   * @param $graph_type - the type of graph that needs drawn On/Off/Mixed
   * @return string - return the pricing array
   */
  public function pricingController($graph_choice, $graph_type){
      //TODO: Set all constants before we move forward need to be moved into dynamic user input

    //set all of the date and graph type constants
    $this->setTerms($graph_choice, $graph_type);

    //Grab the required data out of the on and off peak tables
    $pricingHolder = $this->queryData($graph_choice, $graph_type);
    $this->septemberFix();

    //Reset dates and calculate the price points to be graphed.
    $this->setTerms($graph_choice, $graph_type);
    $pricing = $this->pricingGeneration($pricingHolder, $graph_choice, $graph_type);

    //TODO: Add multi-series support here
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
  public function queryData($graph_choice, $graph_type){
    //TODO: Change to 2D Array to handle multiple series
    $temp_array = array();

    //TODO: Turn this into a For Loop with a Do/While inside of it for multiple series
    $query = 'Select purchase_date, ';
    do{
      $query = $query . $this->TERM_START->format('M_y, ');
      $this->ARRAY_DATE_KEYS[] = $this->TERM_START->format('M_y');
      $this->TERM_START->add(new \DateInterval('P1M'));
    }while($this->TERM_START < $this->TERM_END);

    //Remove the last comma
    $query = preg_replace('/,([^,]*)$/', ' \1', $query);

    //Swap September's abbreviation to match the data set and query on peak numbers
    $query = str_replace('Sep', 'Sept', $query);

    //Reset dates
    $this->setTerms($graph_choice, $graph_type);

    //Grab only the appropriate data
    if($graph_type == 'On Peak'){
      $query.= "FROM ppsweb_pricemodel.elec_on_peak WHERE purchase_date >= '". $this->PRICING_START->format('Y-m-d') . "' ORDER BY purchase_date";
      $temp_array[0] = db_query($query)->fetchAllAssoc('purchase_date');
    }

    else if($graph_type == 'Off Peak'){
      $query.= "FROM ppsweb_pricemodel.elec_off_peak WHERE purchase_date >= '". $this->PRICING_START->format('Y-m-d') . "' ORDER BY purchase_date";
      $temp_array[0] = db_query($query)->fetchAllAssoc('purchase_date');
    }

    else{
      $query.= "FROM ppsweb_pricemodel.elec_on_peak WHERE purchase_date >= '". $this->PRICING_START->format('Y-m-d') . "' ORDER BY purchase_date";
      $temp_array[0] = db_query($query)->fetchAllAssoc('purchase_date');

      //modify the query string to grab the off peak numbers.
      $query = str_replace('elec_on_peak', 'elec_off_peak', $query);
      $temp_array[1] = db_query($query)->fetchAllAssoc('purchase_date');
    }

    return $temp_array;
  }

  /**
   * Generates the requested pricing points and returns them to the view
   *
   * @param $data_array - queried array returned from queryData
   * @param $graph_type - Type of graph -> 'On Peak', 'Off Peak', 'Mixed'
   * @return string - placeholder return documentation
   */
  //TODO: Add an argument to let this function know how many series we need to generate
  public function pricingGeneration($data_array, $graph_choice, $graph_type){
    $temp_array = array(array(array()));
    $monthly_on_total = 0;
    $monthly_off_total = 0;

    //Run the loop one to three times depending upon the request
    //TODO: Add multiple series support
    for($i=0; $i<1; $i++){
      $zero_counter = 0;
      $current_keys = array_keys($data_array[$i]);
      $this->MAX_DATE = new \DateTime($current_keys[sizeof($current_keys) - 3]);
      //do/while loop runs from PRICING_START until MAX_DATE incrementing one day at a time
      do{
        //On or Off Peak Pricing
        $current_day_formatted = $this->PRICING_START->format('Y-m-d');
        if(isset($data_array[$i][$current_day_formatted])){
          for($j=0; $j<12; $j++){
            $current_date_key = $this->ARRAY_DATE_KEYS[$j];
            $current_price = $data_array[$i][$current_day_formatted]->$current_date_key;
            //Zero Counter is probably not necessary here.
            if($current_price > 0.0001){
              $monthly_on_total += (($current_price / 1000) * $this->MONTHLY_USAGE);
            }
            else{
              $zero_counter++;
            }
          }
        }
        //TODO: Mixed Pricing

        /**
         * Calculate the price for today and add it to the pricing list
         *
         * Price = (Term Cost / (Term Vol - (Num Zeroes * 100K ))
         */
        if($monthly_on_total > 0.00001 && $zero_counter < 12){
          $price = ($monthly_on_total + $monthly_off_total) / (($this->MONTHLY_USAGE * 12) - ($zero_counter * $this->MONTHLY_USAGE));
          //$temp_array[For Loop(Series#)[Date][Price]
          $temp_array[$i][$this->PRICING_START->format('Y-m-d')][] = $price;
        }
        else{
          //We have 12 0's nothing is necessary
        }
        //Flush variables
        $monthly_on_total = 0;
        $monthly_off_total = 0;
        $zero_counter = 0;
        $this->PRICING_START->add(new \DateInterval('P1D'));
        //TODO: Cast into $pricing_array for multiple series support
      }while($this->PRICING_START < $this->MAX_DATE);
    }
    unset($temp_array[0][0]);
    return $temp_array;
  }

  public function septemberFix(){
    for($i=0; $i<sizeof($this->ARRAY_DATE_KEYS); $i++){
        $this->ARRAY_DATE_KEYS[$i] = str_replace('Sep', 'Sept', $this->ARRAY_DATE_KEYS[$i]);
    }
  }

  public function setTerms($graph_choice, $graph_type){
    /**
     * Graph Choice can be: 2015, 2016, 2017, 2018, 2019, (2015, 2016, 2017), (2016, 2017, 2018)
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
    return 'FOO';
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