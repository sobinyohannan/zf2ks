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
        var_dump($user);
        $id = (int)$user->user_id;
        if($id == 0) {
            $this->tableGateway->insert($data);
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
}

