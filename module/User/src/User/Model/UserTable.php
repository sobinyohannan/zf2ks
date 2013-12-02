<?php

/* 
 * Table Gateway Class for User Model
 * Author   : Sobin
 * Date     : 28 Nov 2013
 * 
 */

namespace User\Model;

use Zend\Db\TableGateway\TableGateway;

Class UserTable {
    
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
    
    /*
     * Fetch All Records
     * Author   : Sobin
     * Date     : 28 Nov 2013
     */
    public function fetchAll() {
        $res    =   array();        
        $resultSet  =   $this->tableGateway->select(); 
        foreach($resultSet as $r){
            $res[]  =   $r;
        }        
        return $res;
    }
    
    /*
     * Save data to the user_general table
     * Author   : Sobin
     * Date     : 28 Nov 2013
     * @param   : User data object
     */
    public function saveUser($user) {
        $data = (array) $user;
        unset($data['inputFilter']);        
        $id = (int)$user->user_id;
        if($id == 0) {            
            $result = $this->tableGateway->insert($data);
            $id = $this->tableGateway->lastInsertValue;
            if($result == 1) {
                return array('status' => TRUE, 'id' => $id);
            }
            else {
                return array('status' => FALSE);
            }
                
            
        }
        /*else {
            if($this->getPositionInfo($id)) {
                $this->tableGateway->update($data,array('id' => $id));
            }
            else {
                throw new \Exception("Form Id does not exist");
            }
        }*/
    }
    
    /*
     * Activate user
     * @author     : Sobin
     * @date       : 2 Dec 2013
     * @param      : $user_id
     * @return     : True or False 
     */
    public function activateUser($user_id) {
        $data = array(
          'mail_status' => 1  
        );
        $result = $this->tableGateway->update($data,array('user_id' => $user_id));
        if($result) {
            return true;
        }
        else {
            return false;
        }
        
    }
}

