<?php
/**
 * Created by PhpStorm.
 * User: alexm
 * Date: 6/1/2016
 * Time: 1:48 PM
 */

namespace Drupal\pps_energy_tracker\Form;

//TODO: Document entire class
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class GenericGraphForm extends FormBase{

    /**
     * {@inheritdoc}
     */
    public function getFormId(){
        return 'GenericGraphForm';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state){
        //TODO: Switch selected to be a dynamic variable
        $selected = 'This is default selected';
        $form['graph_name'] = array(
            '#type' => 'select',
            '#title' => 'Graph: ',
            '#options' => ['2015', '2016', '2017', '2018', '2015, 2016, 2017', '2016, 2017, 2018'],
            '#description' => $this->t('Select A Graph'),
            /**'#ajax' => array(
                'callback' => '$this->ajax_call_graph_type(&$form, $form_state)',
                'wrapper' => 'graph-select-wrapper',
            ),**/
        );

        //TODO: Have this be responsive to the graph_name select
        $form['graph_type'] = array(
            '#type' =>'select',
            '#title' => $selected . ' Graph Types',
            '#prefix' => '<div id="graph-type-wrapper">',
            '#suffix' => '</div>',
            '#description' => $this->t('Select A Graph Type'),
           // '#options' => $this->ajax_graph_type_options($selected),
            '#options' => ['A', 'B', 'C', 'D'],
        );


        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit')
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array  &$form, FormStateInterface $form_state){
        //TODO: Validate form input
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state){
        return parent::submitForm($form, $form_state);
    }

    /**
     * AJAX call to redraw the Graph Type select box with new options
     * @param array $form
     * @param FormStateInterface $form_state
     * @return mixed
     */
    public function ajax_call_graph_type(array &$form, FormStateInterface $form_state){
        return $form['graph_type'];
    }

    /**
     * AJAX call to populate the Graph Type select box
     * @param $selected
     * @return array
     */
    public function ajax_graph_type_options($selected){
        return ['A', 'B', 'C', 'D', 'E'];
    }
}