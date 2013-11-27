<?php

/*
 * Form class for 'Login Page'
 * Author   : Sobin
 * 
 */

namespace Auth\Form;

use Zend\Form\Form;
use Zend\Form\Element;

Class LoginForm extends Form
{
    // Constructor for the Form
    public function __construct($name = null, $options = array()) {
        parent::__construct('auth');
        
        $this->setAttribute('method', 'POST');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));
        // Add title field
        $this->add(array(
           'name' => 'username',
            'attributes' => array(
                'type' => 'text',                
            ),
            'options' => array(
                'label' => 'Username'
            )
        ));
        //Add content field
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Password'
            )
        ));
        
        // Remember Me
        $this->add(array(
            'name' => 'remember-me',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes'    => array(
                'type'      => 'checkbox',
                'id'        => 'remember-me',
                'checked'   => 'checked'
            ),
            'options' => array(
                'label'     => 'Remember Me'
            ),
        ));
        // Submit button
        $this->add(array(
            'name'  => 'submit',
            'attributes'    => array(
                'type'      => 'submit',
                'value'     => 'Login',
                'id'        => 'submit-button'
            )
        ));
    }
}