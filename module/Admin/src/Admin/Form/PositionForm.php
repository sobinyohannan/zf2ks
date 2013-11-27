<?php

/*
 * Class Desc : Form for add new position page 
 * 
 */

namespace Admin\Form;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

Class PositionForm extends Form {

    public function __construct($name = null, $options = array()) {

        parent::__construct('admin');
        $this->setAttribute('method', 'POST');
        // create fields
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));
        
        $work_name = new Element('work_name');
        $work_name->setLabel('Position Name ');
        $work_name->setAttributes(array(
            'type' => 'text'
        ));

        $desc_small = new Element('description_small');
        $desc_small->setLabel('Oneliner ');
        $desc_small->setAttributes(array(
            'type' => 'text'
        ));

        $desc_detailed = new Element\Textarea('description_big');
        $desc_detailed->setLabel('Detailed Description ');

        $tips = new Element('tips');
        $tips->setLabel('Tips ');
        $tips->setAttributes(array(
            'type' => 'text'
        ));

        $image_file = new Element\File('work_image_list');
        $image_file->setLabel('Image File');

        $video_file = new Element\File('work_video_list');
        $video_file->setLabel('Video File');
        
        $send = new Element('submit');
        $send->setValue('Submit');
        $send->setAttributes(array(
            'type' => 'submit'
        ));

        //Add fileds to form
        $this->add($work_name);
        $this->add($desc_small);
        $this->add($desc_detailed);
        $this->add($tips);
        $this->add($image_file);
        $this->add($video_file);
        // Radio buttons for free position or not
        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'work_rate',
            'options' => array(
                'label' => 'Free Position?',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes' => array(
                'value' => '0'
            )
        ));
        // Activate Position checkbox
        $this->add(array(
             'type' => 'Zend\Form\Element\Checkbox',
             'name' => 'isactive',
             'options' => array(
                     'label' => 'Activate Position',
                     'use_hidden_element' => true,
                     'checked_value' => '1',
                     'unchecked_value' => '0'
             )
        ));
        $this->add($send);
    }

}
