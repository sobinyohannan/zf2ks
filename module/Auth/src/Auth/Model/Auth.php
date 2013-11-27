<?php

/* 
 * Main Model class for Blog Module
 */

namespace Auth\Model;

use Zend\InputFilter\Factory as InputFactory; 
use Zend\InputFilter\InputFilter; 
use Zend\InputFilter\InputFilterAwareInterface; 
use Zend\InputFilter\InputFilterInterface;

Class Auth implements InputFilterAwareInterface
{
    public $id;
    public $username;
    public $password;
    protected $inputFilter;
    
    // Implement this for communicating with TableGateway
    //  Copies data from the passed array to the entity variables
    public function exchangeArray($data)
    {
        $this->id       =  (isset($data['id'])) ? $data['id']  : NULL;
        $this->username    =   (isset($data['username'])) ?   $data['username']  :   NULL;
        $this->content  =   (isset($data['password']))   ?   $data['password']    : NULL;        
        
    } 
    
    // Hydrator method for edit
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    // Set input filter
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception('Not used');        
    }
    // Get input filter
    public function getInputFilter() {
        if(!$this->inputFilter) {
            
            $inputFilter = new InputFilter();
            $factory = new InputFactory();            
            
            // Validate id
            $inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                )
            )));
            // Validate title with StringLength
            $inputFilter->add($factory->createInput(array(
                'name' => 'username',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 200,
                        ),
                    ),
                ),
            )));
            // Validate content field
            $inputFilter->add($factory->createInput(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 200,
                        ),
                    ),
                ),
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}

