<?php

/* 
 * User Model Class
 * Author   : Sobin
 * Date     : 28 Nov 2013
 */

namespace User\Model;

use Zend\InputFilter\InputFilter; 
use Zend\InputFilter\InputFilterAwareInterface; 
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;

use Zend\Captcha;
use Zend\Captcha\Image;

Class User implements InputFilterAwareInterface {
    
    public $user_id;
    public $user_first_name;
    public $user_surname;
    public $user_email;
    public $user_username;
    public $user_password;
    public $inputFilter;
    
    /*
     * @Method      : Exchange Data
     * Author       : Sobin     * 
     */
    public function exchangeArray($data) {
        
        $this->user_id = (isset($data['user_id']))?$data['user_id']:NULL;
        $this->user_first_name = (isset($data['user_first_name']))?$data['user_first_name']:NULL;
        $this->user_surname = (isset($data['user_surname']))?$data['user_surname']:NULL;
        $this->user_email = (isset($data['user_email']))?$data['user_email']:NULL;
        $this->user_username = (isset($data['user_username']))?$data['user_username']:$data['user_email'];
        $this->user_password = (isset($data['user_password']))?$data['user_password']:NULL;
    }
    
    /*
     *  Hydrator method for edit
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    /*
     * Implementation of getInputFilter
     * Author   : Sobin
     */
    public function getInputFilter() {
        
        if(!$this->inputFilter) {
            
            $filter = new InputFilter();
            $factory = new InputFactory();
            
            // Create validation rules
            // First name
            $filter->add($factory->createInput(array(
                    'name' => 'user_first_name',
                    'required' => true,
                    'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'min' => 3,
                                'max' => 50,
                            )
                        )
                    )
            )));
            
            //Surname
            $filter->add($factory->createInput(array(
                    'name' => 'user_surname',
                    'required' => true,
                    'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'min' => 3,
                                'max' => 25
                            )
                        )
                    )
            )));
            // Email
            $filter->add($factory->createInput(array(
                    'name' => 'user_email',
                    'required' => true,
                    'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim')
                    ),
                    'validators' => array(                        
                        new \Zend\Validator\EmailAddress(),
                    )                    
            )));
            // Password validation
            $filter->add($factory->createInput(array(
                    'name' => 'user_password',
                    'required' => true,
                    'validators' => array(
                        new \Zend\Validator\Identical('repassword'),
                    )
            )));
            // Retype Password validation
            $filter->add($factory->createInput(array(
                    'name' => 'repassword',
                    'required' => true,
                    'validators' => array(
                        new \Zend\Validator\NotEmpty(),
                    )
            )));
            /*// Captcha
            $this->inputFilter->add($factory->createInput(array(
                    'name' => 'captcha',
                    'required' => true,
                    'validators' => array(                        
                        new \Zend\Validator\captcha(),
                    )                    
            )));*/
            $this->inputFilter = $filter;
        }
        return $this->inputFilter;
    }
    
    /*
     * Implementation of setInputFilter
     * Author   : Sobin
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception('Not used');        
    }
} 


