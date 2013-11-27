<?php

/* Author     : Sobin
 * Class Desc : Model for Position information
 * Date       : 20-11-2013
 * 
 */

namespace Admin\Model; 
use Zend\InputFilter\InputFilter; 
use Zend\InputFilter\InputFilterAwareInterface; 
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory; 

Class Position Implements InputFilterawareInterface
{
    public $id;
    public $work_name;
    public $work_image_list;
    public $work_video_list;
    public $work_exercises;
    public $work_exercises_order;
    public $work_level;
    public $work_recovery_time;
    public $work_recovery_interval;
    public $work_filter;
    public $work_duration;
    public $description_small;
    public $description_big;
    public $tips;
    public $work_rate;
    public $islocked;
    public $isactive;
    public $workout_date;
    public $status;
    public $workout_click_count;
    
    public $inputFilter;
    public function exchangeArray($data,$mode = 'add') {
        //echo '<pre> Inside';print_r($data);
        $this->id = (isset($data['id'])) ? $data['id'] : NULL;
        $this->work_name = (isset($data['work_name'])) ? $data['work_name'] : NULL;        
        $this->work_image_list = (isset($data['work_image_list'])) ? $data['work_image_list'] : NULL;
        $this->work_video_list = (isset($data['work_video_list'])) ? $data['work_video_list'] : NULL;
        $this->work_exercises = (isset($data['work_exercises'])) ? $data['work_exercises'] : NULL;
        $this->work_exercises_order = (isset($data['work_exercises_order'])) ? $data['work_exercises_order'] : NULL;
        $this->work_level = (isset($data['work_level'])) ? $data['work_level'] : NULL;
        $this->work_recovery_time = (isset($data['work_recovery_time'])) ? $data['work_recovery_time'] : NULL;
        $this->work_recovery_interval = (isset($data['work_recovery_interval'])) ? $data['work_recovery_interval'] : NULL;
        $this->work_filter = (isset($data['work_filter'])) ? $data['work_filter'] : NULL;
        $this->work_duration = (isset($data['work_duration'])) ? $data['work_duration'] : NULL;
        $this->description_small = (isset($data['description_small'])) ? $data['description_small'] : NULL;
        $this->description_big = (isset($data['description_big'])) ? $data['description_big'] : NULL;
        $this->tips = (isset($data['tips'])) ? $data['tips'] : NULL;
        $this->work_rate = (isset($data['work_rate'])) ? $data['work_rate'] : NULL;
        $this->islocked = (isset($data['islocked'])) ? $data['islocked'] : NULL;
        $this->isactive = (isset($data['isactive'])) ? $data['isactive'] : NULL;
        $this->workout_date = (isset($data['workout_date'])) ? $data['workout_date'] : NULL;
        $this->status = (isset($data['status'])) ? $data['status'] : NULL;
        $this->workout_click_count = (isset($data['workout_click_count'])) ? $data['workout_click_count'] : NULL;        
        
        if(is_array($this->work_image_list))
        {
            $this->work_image_list = (isset($this->work_image_list['name'])) ? $this->work_image_list['name'] : '';
        }

        if(is_array($this->work_video_list))
        {
            $this->work_video_list = (isset($this->work_video_list['name'])) ? $this->work_video_list['name'] : '';
        }
        
    }
    
    // Hydrator method for edit
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception('Not used');        
    }
    public function getInputFilter() {
        if (!$this->inputFilter) {

            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            // Validate id
            $inputFilter->add($factory->createInput(array(
                        'name' => 'id',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        )
            )));
            // work name
            $inputFilter->add($factory->createInput(array(
                        'name' => 'work_name',
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
            // Oneline description field
            $inputFilter->add($factory->createInput(array(
                        'name' => 'description_small',
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
            // Detailed description field
            $inputFilter->add($factory->createInput(array(
                        'name' => 'description_big',
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
                                    'min' => 10,                                    
                                ),
                            ),
                        ),
            )));
            // Tips field
            $inputFilter->add($factory->createInput(array(
                        'name' => 'tips',
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
                                    'min' => 2,                                    
                                ),
                            ),
                        ),
            )));
            
            $this->inputFilter = $inputFilter;
        }        
        return $this->inputFilter;
    }
}

