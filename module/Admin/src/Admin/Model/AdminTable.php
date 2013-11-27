<?php

/* 
 * Author     : Sobin
 * Class Desc : Table gateway class for Admin Model
 * Date       : 20-11-2013
 * 
 */

namespace Admin\Model;
use Zend\Db\TableGateway\TableGateway;

Class AdminTable 
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway =   $tableGateway;
    }
    
    // Fetch all records
    public function fetchAll()  {
        $res    =   array();        
        $resultSet  =   $this->tableGateway->select(); 
        foreach($resultSet as $r){
            $res[]  =   $r;
        }        
        return $res;
    }  
    
}

