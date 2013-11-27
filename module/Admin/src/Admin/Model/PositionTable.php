<?php

/* 
 * Author     : Sobin
 * Class Desc : Table gateway class for Position Model
 * Date       : 20-11-2013
 * 
 */

namespace Admin\Model;
use \Zend\Db\TableGateway\TableGateway;

Class PositionTable 
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
    
    // Save a position
    public function savePosition(Position $position) {
        //echo "In save position";var_dump($position);die();
        $data = array(
            'work_name' => $position->work_name,
            'description_small' => $position->description_small,
            'description_big' => $position->description_big,
            'tips' => $position->tips,
            //'work_image_list' => $position->work_image_list,
            //'work_video_list' => $position->work_video_list,
            'work_rate' => $position->work_rate,
            'isactive' => $position->isactive,
        );
        if($position->work_image_list != '') 
            $data['work_image_list'] = $position->work_image_list;
        if($position->work_video_list != '')
            $data['work_video_list'] = $position->work_video_list;
        $id = (int)$position->id;
        if($id == 0) {
            $this->tableGateway->insert($data);
        }
        else {
            if($this->getPositionInfo($id)) {
                $this->tableGateway->update($data,array('id' => $id));
            }
            else {
                throw new \Exception("Form Id does not exist");
            }
        }
    }
    
    // Get info of a position
    public function getPositionInfo($pos_id) {
        $id = (int)$pos_id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();        
        if(!$row) {
            throw new \Exception("Could not find!");
        }
        return $row;
    }
    
    // Delete a posiiton
    public function deletePosition($pos_id) {
        $id = (int) $pos_id;
        $this->tableGateway->delete(array('id' => (int) $id));               
        
    }
    
}

