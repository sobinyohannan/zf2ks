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
        
    }
    
    /*
     * Implementation of setInputFilter
     * Author   : Sobin
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception('Not used');        
    }
} 


