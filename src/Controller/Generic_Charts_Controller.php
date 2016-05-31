<?php
/**
 * 
 *
 *
 *
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/30/2016
 * Time: 10:26 AM
 */

namespace Drupal\pps_energy_tracker\Controller;
use Drupal\Core\Render\Element\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Null;

class Generic_Charts_Controller {

    //Class Wide Variables


    public $ON_PEAK_PRICES = array(array()); //On Peak Pricing Data
    public $OFF_PEAK_PRICES = array(array()); //Off Peak Pricing Data
    public $CHART_TYPE = 'NULL'; //Default Chart Type = ON PEAK
    public $TERM_START = null; //When the term starts for pricing
    public $TERM_END = null; //When the term ends for pricing
    public $PRICING_START = null; //Day that we will start the pricing on.
    public $MAX_DATE =  null; //Last day of pricing.
    public $ARRAY_DATE_KEYS = array(); //Need to change to 2D Array to handle multiple series
    public $MONTHLY_USAGE = 100000;
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
        $this->PRICING_START = new \DateTime('2013-03-01');
        $this->TERM_START = new \DateTime('2017-01-01');
        $this->TERM_END = new \DateTime('2017-12-31');
        $this->CHART_TYPE = $graph_type;

        //Grab the required data out of the on and off peak tables
        $pricingHolder = $this->queryData($graph_choice, $graph_type);

        $this->septemberFix();

        //Sort out the data into usable arrays
        //TODO: Remove this if it is no longer necessary
        //$priceArrays = $this->createDataArray($pricingHolder);

        //Calculate the price points to be graphed.
        $pricing = $this->pricingGeneration($pricingHolder);
        return $pricing;
    }

    /**
     * Takes in the graph choice, and generates a query returning the data to the controller
     *
     * @param $graph_choice - graph that needs to be queried Years 2015/2016/2017/2018/etc.
     * @param $graph_type - the type of graph that needs drawn On/Off/Mixed
     * @return array - Return an array containing the queried data 0 - On Peak 1 - Off Peak
     */
    public function queryData($graph_choice, $graph_type){
        //TODO: Change queries to be dependent on user input
        $query = 'Select purchase_date, ';
        do{
            $query = $query . $this->TERM_START->format('M_y, ');
            //TODO: Change to 2D Array to handle multiple series
            $this->ARRAY_DATE_KEYS[] = $this->TERM_START->format('M_y');
            $this->TERM_START->add(new \DateInterval('P1M'));
        }while($this->TERM_START < $this->TERM_END);

        //Reset TERM_START
        //TODO: Change to user input
        $this->TERM_START = new \DateTime('2017-01-01');

        //Remove the last comma
        $query = preg_replace('/,([^,]*)$/', ' \1', $query);

        //Swap September's abbreviation to match the data set and query on peak numbers
        //TODO: Change this to only grab the on peak data if necessary
        $query = str_replace('Sep', 'Sept', $query);
        $query.= "FROM ppsweb_pricemodel.elec_on_peak WHERE purchase_date >= '". $this->PRICING_START->format('Y-m-d') . "' ORDER BY purchase_date";
        $on_data = db_query($query)->fetchAllAssoc('purchase_date');

        //modify the query string to grab the off peak numbers.
        $query = str_replace('elec_on_peak', 'elec_off_peak', $query);
        $off_data = db_query($query)->fetchAllAssoc('purchase_date');

        return [$on_data, $off_data];
    }

    /**
     * Sort out the queried data into usable arrays and return it to the controller
     *
     * Potentially move this into an entity class object?
     *
     * @param $temp_array - Passed in array containing all of the usable data
     * @return array
     */
    //TODO: Remove this is most likely not necessary anymore. Handled via using ->fetchAllAssoc('purchase_date')
    public function createDataArray($temp_array){
        $on_peak = array(array());
        $off_peak = array(array());
        for($i=0; $i<2; $i++){
            for($j=0; $j<sizeof($temp_array[$i]); $j++){
                if($i==0){
                    $on_peak[$temp_array[$i][$j]->purchase_date][] = $temp_array[$i][$j];
                }
                else{
                    $off_peak[$temp_array[$i][$j]->purchase_date][] = $temp_array[$i][$j];
                }
            }
        }
        return [$on_peak, $off_peak];
    }

    /**
     * Generates the requested pricing points and returns them to the view
     *
     * @param $data_array - queried array returned from queryData
     * @return string - placeholder return documentation
     */
    //TODO: Add an argument to let this function know how many series we need to generate
    public function pricingGeneration($data_array){

        //Define the pricing constants that control the do/while loop.
        //TODO: Swap this over to user input once it is functional
        $PRICING_START = new \DateTime('2013-03-01');
        $MAX_DATE = new \DateTime('2016-05-16');

        //Data array holding the date/price array
        $pricing_array = array(array());
        $monthly_on_total = 0;
        $monthly_off_total = 0;

        //Run the loop one to three times depending upon the request
        //TODO: Swap this to run up to three times depending on the graph type. Maybe IF(GRAPH TYPE)->FOR LOOP
        for($i=0; $i<1; $i++){
            //do/while loop runs from PRICING_START until MAX_DATE incrementing one day at a time
            $zero_counter = 0;
            do{
                //For loop controlling the on peak pricing
                for($j=0; $j<12; $j++){
                    $current_key = $this->ARRAY_DATE_KEYS[$j];
                    $current_price = $data_array[$i][$this->PRICING_START->format('Y-m-d')]->$current_key;
                    if($current_price > 0.0001){
                        $monthly_on_total += ($current_price / 1000) * $this->MONTHLY_USAGE;
                    }
                    else{
                        $zero_counter++;
                    }
                }
                //For loop controlling the off peak pricing

                //For loop controlling mixed pricing

                /**
                 * Calculate the price for today and add it to the pricing list
                 *
                 * Price = (Term Cost / (Term Vol - (Num Zeroes * 100K ))
                 */
                $price = ($monthly_on_total + $monthly_off_total) / (($this->MONTHLY_USAGE * 12) - ($zero_counter * $this->MONTHLY_USAGE));
                $pricing_array[$i][$this->PRICING_START] = $price;
                $PRICING_START->add(new \DateInterval('P1D'));
                //TODO: Flush variables?
            }while($PRICING_START < $MAX_DATE);
        }
        return $pricing_array;
    }

    /**
     * Utility Function tests the start date to see if it will need moved.
     * Move Scenario: Current start date has no meaningful data
     *
     * Calls moveStartDate if a 'Move Scenario' is met
     *
     * @param $current_start - the date that we are looking to start pricing.
     */
    public function testStartData($current_start){

    }

    /**
     * Utility function called from testStartDate slides the current start date forward by a day
     *
     * Calls back to testStartDate with the moved date
     *
     * @param $current_start - current starting date passed from testStartDate
     */
    public function moveStartDate($current_start){

    }

    public function septemberFix(){
        for($i=0; $i<sizeof($this->ARRAY_DATE_KEYS); $i++){
            $this->ARRAY_DATE_KEYS[$i] = str_replace('Sep', 'Sept', $this->ARRAY_DATE_KEYS[$i]);
        }
    }
}