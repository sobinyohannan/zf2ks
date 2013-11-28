<?php

/* 
 * @Desc       : User Registration Form
 * Author      : Sobin
 * Date        : 28 Nov 2013
 */

namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Captcha;
use Zend\Captcha\Dumb;

Class RegisterForm extends Form {
    
    /*
     *  Constructor
     *  Author  : Sobin
     *  Date    : 28 Nov 2013
     */
    public function __construct($name = null, $options = array()) {
        parent::__construct('user');
        $this->setAttribute('method', 'POST');
        // create fields
        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));
        
        // First name
        $first_name = new Element('user_first_name');
        $first_name->setLabel('First Name');
        $first_name->setAttributes(array(
            'type' => 'text'
        ));
        
        // Surname
        $sur_name = new Element('user_surname');
        $sur_name->setLabel('Surname');
        $sur_name->setAttributes(array(
            'type' => 'text'
        ));
        
        // Email
        $user_email = new Element('user_email');
        $user_email->setLabel('Email');
        $user_email->setAttributes(array(
            'type' => 'text'
        ));
        
        // Password
        $password = new Element('user_password');
        $password->setLabel('Password');
        $password->setAttributes(array(
            'type' => 'password'
        ));
        //captcha
        $captcha = new Element\Captcha('captcha');
        $captcha->setCaptcha(new Captcha\Dumb())
            ->setLabel('Please verify you are human');
        // Submit
        $send = new Element('submit');
        $send->setValue('Submit');
        $send->setAttributes(array(
            'type' => 'submit'
        ));
        
        $this->add($first_name);
        $this->add($sur_name);
        $this->add($user_email);
        $this->add($password); 
        $this->add($captcha);
        $this->add($send);
        
    }
    
}



