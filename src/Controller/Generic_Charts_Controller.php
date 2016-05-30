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

class Generic_Charts_Controller {


    /**
     * ->Entry Point Of Class<-
     * Takes in the graph choice, and generates a query returning the data to pricingGeneration
     *
     * @param $graph_choice - graph that needs to be queried Years 2015/2016/2017/2018/etc.
     * @param $graph_type - the type of graph that needs drawn On/Off/Mixed
     */
    public function queryData($graph_choice, $graph_type){

    }

    /**
     * Generates the requested pricing points and returns them to the view
     *
     * @param $data_array - queried array returned from queryData
     */
    public function pricingGeneration($data_array){

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