<?php
/**
 * @Class : Admin Index Controller
 * Author : Sobin
 * Date   : 21-11-2013
 * 
 */

namespace Admin\Controller;

use Admin\Model\Admin;
use Admin\Model\Position;
use Admin\Form\PositionForm;
use Admin\Form\LoginForm;
use Auth\Model\Auth;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Validator\File\Size;

class IndexController extends AbstractActionController
{
    
    /*
     *  Get Positions Table
     *  Author  : Sobin
     */
    public function getPositionTable() {
        $sm =   $this->getServiceLocator();
        try{            
            $posTable = $sm->get('Admin\Model\PositionTable');
        }
        catch(Exception $e) {
            var_dump($e);die();
        }
        return $posTable;
    }
    
    /*
     *  Check authenticated and if true get the authentication identity
     *  Author  : Sobin
     */
    public function getAuthIdentity() {
        if (! $this->getServiceLocator()->get('AuthService')->hasIdentity()){
            return $this->redirect()->toRoute('admin',array('action' => 'login'));
        } 
        return $this->getServiceLocator()->get('AuthService')->hasIdentity();
    }
    
    /*
     *  Admin Index Page
     *  Author  : Sobin
     */
    public function indexAction()
    {   
        $auth = $this->getAuthIdentity();
        /*if (! $this->getServiceLocator()->get('AuthService')->hasIdentity()){
            return $this->redirect()->toRoute('admin',array('action' => 'login'));
        } 
        $auth = $this->getServiceLocator()->get('AuthService')->hasIdentity();*/
        $this->layout()->auth = $auth;        
        return new ViewModel();
    }
    
    /*
     *  Positions Index Page
     *  Author  : Sobin
     */
    public function positionsAction() {
        $auth = $this->getAuthIdentity();
        $this->layout()->auth = $auth;
        $positions = $this->getPositionTable()->fetchAll();        
        $return['positions'] = $positions;
        return $return;
    }
    
    /*
     *  Handle uploading
     *  Author  : Sobin
     */
    public function uploadFile($file,$adapter) {
        $name = $file['name'];
        $size = new Size(array('min' => 2));
        $adapter->setValidators(array($size), $file['name']);         
        if (!$adapter->isValid()){ 
            echo "Adapter Not valid $name";
            var_dump($adapter->getMessages());
            return false;                   
        } else {
            echo "Adapter Valid $name";
            $adapter->setDestination(dirname(__DIR__).'/assets');
            if ($adapter->receive($file['name'])) {
                echo 'Uploaded';
                return true;
            }
            else {
                echo 'Not uploaded';
                return false;
            }
        }        
    }
    
    /*
     *  Set Flash Message
     *  Author  : Sobin
     */
    function setFlashMessage($message) {
        
        $this->flashMessenger()->clearCurrentMessages();
        $this->flashMessenger()->addMessage($message);        
        return $this->flashMessenger()->getCurrentMessages();        
    }
    
    /*
     *  Admin Login Page
     *  Author  : Sobin
     */
    public function loginAction() {
        if($this->getServiceLocator()->get('AuthService')->hasIdentity()) {
           $this->redirect()->toRoute('admin',array('action' => 'index'));
       }       
       $form = new LoginForm();
       $form->get('submit')->setValue('Login');
       // Handle Login submit here
       //$blogTable   =   $this->getBlogTable();
       $request = $this->getRequest();        
       if($request->isPost()) {        
           $login = new Auth();
           $form->setInputFilter($login->getInputFilter());
           $form->setData($request->getPost());
           if($form->isValid()) {                            
                           
               $this->getServiceLocator()->get('AuthService')->getAdapter()->setIdentity($request->getPost('username'))->setCredential($request->getPost('password'));

               $result = $this->getServiceLocator()->get('AuthService')->authenticate();
               //return $result->get;
               $result = array('code' => $result->getCode(),'Identity' => $result->getIdentity(),'message' => $result->getMessages());
               if($result['code'] === 1) {
                    // Set remember me
                    if($request->getPost('remember-me') == 1) {
                        $this->getServiceLocator()->get('Auth\Model\MyAuthStorage')->setRememberMe(1);
                        $this->getServiceLocator()->get('AuthService')->setStorage($this->getServiceLocator()->get('Auth\Model\MyAuthStorage'));
                    }
                    $this->getServiceLocator()->get('AuthService')->getStorage()->write($request->getPost('username'));
                    $this->redirect()->toRoute('admin');
               }
                    
                else {
                    $return['messages'] = $this->setFlashMessage('Login Failed : '.$result['message'][0]);                    
                }
           }
           else {
               $return['messages'] = $this->setFlashMessage('Fill up username and password');               
           }
       }
       //$return['form'] = $form;
       $view =new ViewModel(
               array(
                   'form' => $form,
                   'messages' => isset($return['messages'])?$return['messages']:array(''),
               )
       );
       //$view->layout('layout/login');       
       $this->layout('layout/login');
       return $view;
    }
    
    /*
     *  Add new position
     *  Author  : Sobin
     */
    public function addPositionAction() {
        $auth = $this->getAuthIdentity();
        $this->layout()->auth = $auth;
        $form = new PositionForm();
        // Handle the form submit here
        $request = $this->getRequest();
        if($request->isPost()) {
            $videoFile    = $this->params()->fromFiles('work_video_list'); 
            $imageFile    = $this->params()->fromFiles('work_image_list');            
            //var_dump($imageFile);die();
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $position = new Position();            
            $form->setData($post);
            $form->setInputFilter($position->getInputFilter());
            if($form->isValid()) {                                       
                $position->exchangeArray($form->getData());  
                $this->getPositionTable()->savePosition($position);                
                // Upload the video file
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                // Image Upload
                $size = new Size(array('min' => 2));
                //$adapter->setDestination(dirname(__DIR__).'/assets');
                // Get Base
                $uri = $this->getRequest()->getUri();
                $scheme = $uri->getScheme();
                $host = $uri->getHost();
                $base = $scheme.'://'.$host;//sprintf('%s://%s', $scheme, $host);
                $target = './public/uploads/admin/';//$base.'/uploads/admin/';
                //echo 'b4'.realpath($target);var_dump(is_dir($target));exit;
                $adapter->setDestination($target);
                $adapter->setValidators(array($size), $imageFile['name']);  
                $adapter->setValidators(array($size), $videoFile['name']);
                try {
                    $adapter->receive($imageFile['name']);
                    $adapter->receive($videoFile['name']);
                    $this->redirect()->toRoute('admin',array('action' => 'positions'));
                }
                catch(Exception $e) {
                    $return['messages'] = "Error occurred on uploading.";
                }                
                              
            }
            else {
                $return['messages'] = $this->setFlashMessage('Fill the required fields');  
            }
        }
        $return['form'] = $form;        
        return $return;
    }
    
    /*
     *  Edit position page
     *  Author  : Sobin
     */
    public function editPositionAction() {        
        $pos_id = (int) $this->params()->fromRoute('id',0);
        // Redirect if Id missing        
        if(!$pos_id) {
            $this->redirect()->toRoute('admin',array('action' => 'positions'));
        }
        try {
            $position1 = $this->getPositionTable()->getPositionInfo($pos_id); 
        }
        catch(Exception $e) {
            $this->redirect()->toRoute('admin',array('action' => 'positions'));
        }       
        
        
        $form = new PositionForm();
        $form->get('submit')->setValue('Edit');        
        $form->bind($position1);
        
        // Handle the update here
        $request = $this->getRequest();
        if($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $position = new Position();            
            $form->setData($post);            
            $form->setInputFilter($position->getInputFilter());            
            if($form->isValid()) {                
                $position->exchangeArray($post);                
                //check if null image or video passed
                if($position->work_image_list == NULL || $position->work_image_list == '')
                {
                    $position->work_image_list = $position1->work_image_list;
                }
                
                if($position->work_video_list == NULL || $position->work_video_list == '')
                {
                    $position->work_video_list = $position1->work_video_list;
                }                 
                $this->getPositionTable()->savePosition($position);               
                
                
                // Upload the video file
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                // Image Upload
                $size = new Size(array('min' => 2));
                //$adapter->setDestination(dirname(__DIR__).'/assets');
                // Get Base
                $uri = $this->getRequest()->getUri();
                $scheme = $uri->getScheme();
                $host = $uri->getHost();
                $base = $scheme.'://'.$host;//sprintf('%s://%s', $scheme, $host);
                $target = './public/uploads/admin/';//$base.'/uploads/admin/';                
                
                $videoFile    = $this->params()->fromFiles('work_video_list'); 
                $imageFile    = $this->params()->fromFiles('work_image_list'); 
                $adapter->setDestination($target);
                $adapter->setValidators(array($size), $imageFile['name']);  
                $adapter->setValidators(array($size), $videoFile['name']);
                try {                    
                    $adapter->receive($imageFile['name']);
                    $adapter->receive($videoFile['name']);
                    $this->redirect()->toRoute('admin',array('action' => 'positions'));
                }
                catch(Exception $e) {
                    $return['messages'] = "Error occurred on uploading.";
                }   
                // Redirect to Index
                $this->redirect()->toRoute('admin',array('action' => 'positions'));
            }            
        }
                
        return array(
            'id' => $pos_id,
            'form' => $form,
            'position' => $position1
        );
    }
    
    /*
     *  Delete a position
     *  Author  : Sobin
     */
    public function deletePositionAction() {
        // check Id param set, if not redirect
        $pos_id = (int) $this->params()->fromRoute('id',0);
        if(!$pos_id) {
            $this->redirect()->toRoute('admin',array('action' => 'positions'));
        }
        $request = $this->getRequest();
        if($request->isPost()) {
            $del = $request->getPost('del','No');
            
            if($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getPositionTable()->deletePosition($id);
            }
            
            $this->redirect()->toRoute('admin',array('action' => 'positions'));
        }    
        $view = new ViewModel(array(
            'id'    => $pos_id,
            'position' => $this->getPositionTable()->getPositionInfo($pos_id)
        ));
        // Disable layouts; `MvcEvent` will use this View Model instead
        //$view->setTerminal(true);
        return $view;
    }
    
    /*
     *  Amin User Logout
     *  Author  : Sobin
     */
    public function logoutAction() {
            
       $this->getServiceLocator()->get('AuthService')->clearIdentity();
       $this->getServiceLocator()->get('Auth\Model\MyAuthStorage')->forgetMe();
       $this->redirect()->toRoute('admin',array('action' => 'login'));
    }
    
    
}
