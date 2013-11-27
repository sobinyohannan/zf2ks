<?php

/* 
 * Table Gateway Class for User Autherntication
 * 
 */
namespace Auth\Model;
use Zend\Db\TableGateway\TableGateway;

Class AuthTableUser 
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


