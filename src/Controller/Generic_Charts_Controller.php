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
    public $MAX_DATE =  null; //Last day of pricing.
    public $PRICING_START = null; //Day that we will start the pricing on.
    public $ON_PEAK_PRICES = array(array());
    public $OFF_PEAK_PRICES = array(array());
    /**
     * ->Entry Point Of Class<-
     * Makes the initial function call to start calculating the pricing data.
     *
     * @param $graph_choice - graph that needs to be queried Years 2015/2016/2017/2018/etc.
     * @param $graph_type - the type of graph that needs drawn On/Off/Mixed
     * @return string - return the pricing array
     */
    public function pricingController($graph_choice, $graph_type){
        //Grab the required data out of the on and off peak tables
        $pricingHolder = $this->queryData($graph_choice, $graph_type);

        //Calculate the price points to be graphed.
        $pricing = $this->pricingGeneration($pricingHolder);
        return $pricing;
    }

    /**
     * Takes in the graph choice, and generates a query returning the data to pricingGeneration
     *
     * @param $graph_choice - graph that needs to be queried Years 2015/2016/2017/2018/etc.
     * @param $graph_type - the type of graph that needs drawn On/Off/Mixed
     * @return array - Return an array containing the queried data 0 - On Peak 1 - Off Peak
     */
    public function queryData($graph_choice, $graph_type){
        /**
         * 1:Query On/Off Peak and store in separate arrays
         * 2:Return arrays to the controller
         */
        //TODO: Change this to user input
        $PRICING_START = new \DateTime('2013-03-01');
        $TERM_START = new \DateTime('2017-01-01');
        $TERM_END = new \DateTime('2017-12-31');

        $query = 'Select purchase_date, ';
        do{
            $query = $query . $TERM_START->format('M_y, ');;
            $TERM_START->add(new \DateInterval('P1M'));
        }while($TERM_START < $TERM_END);

        //Remove the last comma
        $query = preg_replace('/,([^,]*)$/', ' \1', $query);

        //Swap September's abbreviation to match the data set and query on peak numbers
        //TODO: Change this to only grab the on peak data if necessary
        $query = str_replace('Sep', 'Sept', $query);
        $query.= "FROM ppsweb_pricemodel.elec_on_peak WHERE purchase_date >= '". $PRICING_START->format('Y-m-d') . "' ORDER BY purchase_date";
        $on_data = db_query($query)->fetchAll();

        //modify the query string to grab the off peak numbers.
        $query = str_replace('elec_on_peak', 'elec_off_peak', $query);
        $off_data = db_query($query)->fetchAll();

        return [$on_data, $off_data];
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

        //Run the loop one to three times depending upon the request
        //TODO: Swap this to run up to three times depending on the graph type
        for($i=0; $i<1; $i++){
            do{
                //For loop controlling the on peak pricing

                //For loop controlling the off peak pricing

                //For loop controlling mixed pricing

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
}